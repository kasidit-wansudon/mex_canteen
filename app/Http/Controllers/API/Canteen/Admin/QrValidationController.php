<?php

namespace App\Http\Controllers\API\Canteen\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Canteen\Admin\ValidateQrRequest;
use App\Models\CollectionLog;
use App\Models\MealCollection;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class QrValidationController extends Controller
{
    /**
     * Validate and consume a reservation QR token.
     *
     * @param  \App\Http\Requests\Canteen\Admin\ValidateQrRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateQr(ValidateQrRequest $request)
    {
        $scanner = $request->user();
        $token = $request->input('qr_code_token');

        $reservation = Reservation::query()
            ->with(['mealPlan', 'user'])
            ->where('qr_code_token', $token)
            ->first();

        if (!$reservation) {
            $this->createLog(null, $scanner, $token, 'failed', 'not_found', 0);

            return response()->json([
                'result' => 'failed',
                'message' => '二维码无效',
            ], 422);
        }

        if ($reservation->status === 'collected') {
            $this->createLog($reservation, $scanner, $token, 'failed', 'already_used', $reservation->meal_count);

            return response()->json([
                'result' => 'failed',
                'message' => '二维码已使用',
            ], 422);
        }

        if ($reservation->status === 'cancelled') {
            $this->createLog($reservation, $scanner, $token, 'failed', 'cancelled', $reservation->meal_count);

            return response()->json([
                'result' => 'failed',
                'message' => '二维码已取消',
            ], 422);
        }

        if ($reservation->qr_expiry_time && now()->greaterThan($reservation->qr_expiry_time)) {
            $reservation->forceFill(['status' => 'expired'])->save();
            $this->createLog($reservation, $scanner, $token, 'failed', 'expired', $reservation->meal_count);

            return response()->json([
                'result' => 'failed',
                'message' => '二维码过期',
            ], 422);
        }

        DB::transaction(function () use ($reservation, $scanner, $token) {
            $reservation->forceFill([
                'status' => 'collected',
                'collected_at' => now(),
            ])->save();

            MealCollection::query()->updateOrCreate(
                ['reservation_id' => $reservation->id],
                [
                    'collector_user_id' => $scanner->id,
                    'collector_staff_code' => $scanner->staff_code,
                    'collector_name' => $scanner->staff_name,
                    'collected_meal_count' => $reservation->meal_count,
                    'collection_method' => 'qr',
                    'collected_at' => now(),
                ]
            );

            $this->createLog($reservation, $scanner, $token, 'pass', null, $reservation->meal_count);
        });

        return response()->json([
            'result' => 'pass',
            'message' => sprintf('可就餐：%d份', (int) $reservation->meal_count),
            'data' => [
                'reservation_id' => $reservation->id,
                'meal_count' => (int) $reservation->meal_count,
                'staff_code' => optional($reservation->user)->staff_code,
                'staff_name' => optional($reservation->user)->staff_name,
            ],
        ]);
    }

    /**
     * @param  \App\Models\Reservation|null  $reservation
     * @param  \App\Models\User  $scanner
     * @param  string|null  $token
     * @param  string  $result
     * @param  string|null  $reason
     * @param  int  $mealCount
     * @return void
     */
    private function createLog($reservation, $scanner, $token, $result, $reason, $mealCount)
    {
        CollectionLog::query()->create([
            'reservation_id' => $reservation ? $reservation->id : null,
            'scanner_user_id' => $scanner->id,
            'scanner_staff_code' => $scanner->staff_code,
            'qr_code_token' => $token,
            'scan_result' => $result,
            'failure_reason' => $reason,
            'meal_count' => $mealCount,
            'scan_payload' => [
                'scanner_name' => $scanner->staff_name,
                'reservation_status' => $reservation ? $reservation->status : null,
            ],
            'scanned_at' => now(),
        ]);
    }
}
