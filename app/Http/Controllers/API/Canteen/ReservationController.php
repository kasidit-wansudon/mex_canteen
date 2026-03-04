<?php

namespace App\Http\Controllers\API\Canteen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Canteen\StoreReservationRequest;
use App\Http\Requests\Canteen\UpdateReservationRequest;
use App\Http\Resources\Canteen\ReservationResource;
use App\Models\MealPlan;
use App\Models\Reservation;
use App\Services\Canteen\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * @var \App\Services\Canteen\QrCodeService
     */
    private $qrCodeService;

    /**
     * @param  \App\Services\Canteen\QrCodeService  $qrCodeService
     */
    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * List authenticated user reservations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Reservation::query()
            ->with(['mealPlan', 'mealCollection'])
            ->where('user_id', $user->id)
            ->orderByDesc('reservation_date')
            ->orderByDesc('id');

        if ($request->filled('date_from')) {
            $query->whereDate('reservation_date', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reservation_date', '<=', $request->query('date_to'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json([
            'data' => ReservationResource::collection($query->paginate(20)),
        ]);
    }

    /**
     * Create reservation and generate QR token.
     *
     * @param  \App\Http\Requests\Canteen\StoreReservationRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreReservationRequest $request)
    {
        $user = $request->user();
        $mealPlan = MealPlan::query()->findOrFail($request->input('meal_plan_id'));

        if ($mealPlan->status !== 'published') {
            return response()->json([
                'message' => 'Meal plan is not available.'
            ], 422);
        }

        if ($mealPlan->reservation_close_at && now()->greaterThan($mealPlan->reservation_close_at)) {
            return response()->json([
                'message' => 'Reservation deadline has passed.'
            ], 422);
        }

        $existing = Reservation::query()
            ->where('meal_plan_id', $mealPlan->id)
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['cancelled'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You already have a reservation for this meal plan.',
                'data' => new ReservationResource($existing->load(['mealPlan', 'mealCollection'])),
            ], 422);
        }

        $normalized = $this->normalizeReservationPayload($request->validated());

        if ($normalized['error']) {
            return response()->json(['message' => $normalized['error']], 422);
        }

        $reservation = DB::transaction(function () use ($user, $mealPlan, $normalized) {
            $token = $this->uniqueQrToken();
            $expiryTime = $this->resolveExpiryTime($mealPlan);

            return Reservation::create([
                'meal_plan_id' => $mealPlan->id,
                'user_id' => $user->id,
                'reservation_date' => $mealPlan->meal_date,
                'meal_type' => $mealPlan->meal_type,
                'reservation_type' => $normalized['reservation_type'],
                'visitor_count' => $normalized['visitor_count'],
                'pickup_for_staff_code' => $normalized['pickup_for_staff_code'],
                'pickup_for_user_id' => $normalized['pickup_for_user_id'],
                'meal_count' => $normalized['meal_count'],
                'qr_code_token' => $token,
                'qr_expiry_time' => $expiryTime,
                'status' => 'confirmed',
                'remark' => $normalized['remark'],
            ]);
        });

        return response()->json([
            'message' => 'Reservation created successfully.',
            'data' => new ReservationResource($reservation->load(['mealPlan', 'mealCollection'])),
        ], 201);
    }

    /**
     * Show reservation detail.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $reservation = $this->findAccessibleReservation((int) $id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found.'], 404);
        }

        return response()->json([
            'data' => new ReservationResource($reservation),
        ]);
    }

    /**
     * Return QR payload for reservation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function qr($id)
    {
        $reservation = $this->findAccessibleReservation((int) $id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found.'], 404);
        }

        $payload = $this->qrCodeService->buildPayload($reservation);

        return response()->json([
            'data' => [
                'reservation' => new ReservationResource($reservation),
                'payload' => $payload,
                'qr_content' => json_encode($payload),
                'is_expired' => $this->qrCodeService->isExpired($reservation),
            ],
        ]);
    }

    /**
     * Update reservation before meal deadline.
     *
     * @param  \App\Http\Requests\Canteen\UpdateReservationRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateReservationRequest $request, $id)
    {
        $reservation = $this->findAccessibleReservation((int) $id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found.'], 404);
        }

        if (!$reservation->isEditable()) {
            return response()->json(['message' => 'Reservation can no longer be edited.'], 422);
        }

        $normalized = $this->normalizeReservationPayload($request->validated());

        if ($normalized['error']) {
            return response()->json(['message' => $normalized['error']], 422);
        }

        $reservation->fill([
            'reservation_type' => $normalized['reservation_type'],
            'visitor_count' => $normalized['visitor_count'],
            'pickup_for_staff_code' => $normalized['pickup_for_staff_code'],
            'pickup_for_user_id' => $normalized['pickup_for_user_id'],
            'meal_count' => $normalized['meal_count'],
            'remark' => $normalized['remark'],
            'status' => 'confirmed',
            'cancelled_at' => null,
            'qr_code_token' => $this->uniqueQrToken(),
            'qr_expiry_time' => $this->resolveExpiryTime($reservation->mealPlan),
        ]);
        $reservation->save();

        return response()->json([
            'message' => 'Reservation updated successfully.',
            'data' => new ReservationResource($reservation->load(['mealPlan', 'mealCollection'])),
        ]);
    }

    /**
     * Cancel reservation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $reservation = $this->findAccessibleReservation((int) $id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found.'], 404);
        }

        if (in_array($reservation->status, ['collected', 'cancelled'], true)) {
            return response()->json([
                'message' => 'Reservation cannot be cancelled in current status.'
            ], 422);
        }

        $reservation->forceFill([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Reservation cancelled successfully.'
        ]);
    }

    /**
     * @param  int  $reservationId
     * @return \App\Models\Reservation|null
     */
    private function findAccessibleReservation($reservationId)
    {
        $user = request()->user();

        $query = Reservation::query()
            ->with(['mealPlan', 'user', 'visitor', 'mealCollection'])
            ->where('id', $reservationId);

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return $query->first();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizeReservationPayload(array $payload)
    {
        $type = $payload['reservation_type'];
        $visitorCount = (int) ($payload['visitor_count'] ?? 0);
        $pickupStaffCode = $payload['pickup_for_staff_code'] ?? null;
        $pickupForUserId = null;
        $baseMeals = 0;

        if (in_array($type, ['self', 'self_invitation', 'self_pickup'], true)) {
            $baseMeals = 1;
        }

        if ($type === 'pickup_only') {
            $baseMeals = 1;
        }

        if (in_array($type, ['self_invitation', 'invitation_only'], true) && $visitorCount <= 0) {
            return ['error' => 'Visitor count must be greater than zero for invitation reservations.'];
        }

        if (!in_array($type, ['self_invitation', 'invitation_only'], true)) {
            $visitorCount = 0;
        }

        if (in_array($type, ['self_pickup', 'pickup_only'], true)) {
            if (empty($pickupStaffCode)) {
                return ['error' => 'Pickup staff code is required for pickup reservations.'];
            }

            $pickupUser = \App\Models\User::query()->where('staff_code', $pickupStaffCode)->first();
            if ($pickupUser) {
                $pickupForUserId = $pickupUser->id;
            }

            if ($type === 'self_pickup') {
                $baseMeals += 1;
            }
        } else {
            $pickupStaffCode = null;
        }

        return [
            'error' => null,
            'reservation_type' => $type,
            'visitor_count' => $visitorCount,
            'pickup_for_staff_code' => $pickupStaffCode,
            'pickup_for_user_id' => $pickupForUserId,
            'meal_count' => $baseMeals + $visitorCount,
            'remark' => $payload['remark'] ?? null,
        ];
    }

    /**
     * @param  \App\Models\MealPlan  $mealPlan
     * @return \Carbon\Carbon
     */
    private function resolveExpiryTime(MealPlan $mealPlan)
    {
        if ($mealPlan->collection_end_at) {
            return Carbon::parse($mealPlan->collection_end_at);
        }

        $date = Carbon::parse($mealPlan->meal_date);

        if ($mealPlan->meal_type === 'lunch') {
            return $date->copy()->setTime(14, 30, 0);
        }

        return $date->copy()->setTime(20, 30, 0);
    }

    /**
     * @return string
     */
    private function uniqueQrToken()
    {
        do {
            $token = $this->qrCodeService->generateToken();
        } while (Reservation::query()->where('qr_code_token', $token)->exists());

        return $token;
    }
}
