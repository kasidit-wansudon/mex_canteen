<?php

namespace App\Http\Controllers\API\Canteen\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Canteen\Admin\StoreVisitorRequest;
use App\Http\Requests\Canteen\Admin\UpdateUserStatusRequest;
use App\Http\Resources\Canteen\UserResource;
use App\Http\Resources\Canteen\VisitorResource;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Return users by filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::query()->orderBy('staff_code');

        if ($request->filled('staff_type')) {
            $query->where('staff_type', $request->query('staff_type'));
        }

        if ($request->filled('staff_code')) {
            $query->where('staff_code', 'like', '%' . $request->query('staff_code') . '%');
        }

        if ($request->filled('staff_name')) {
            $query->where('staff_name', 'like', '%' . $request->query('staff_name') . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->query('email') . '%');
        }

        if ($request->filled('account_status')) {
            $query->where('account_status', (bool) $request->query('account_status'));
        }

        return response()->json([
            'data' => UserResource::collection($query->paginate(20)),
        ]);
    }

    /**
     * Create temporary visitor account.
     *
     * @param  \App\Http\Requests\Canteen\Admin\StoreVisitorRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeVisitor(StoreVisitorRequest $request)
    {
        $validated = $request->validated();

        $visitor = Visitor::query()->create([
            'visitor_code' => $this->generateVisitorCode(),
            'visitor_name' => $validated['visitor_name'],
            'email' => $validated['email'] ?? null,
            'valid_from' => $validated['valid_from'],
            'valid_until' => $validated['valid_until'],
            'account_status' => $validated['account_status'] ?? true,
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Visitor created successfully.',
            'data' => new VisitorResource($visitor),
        ], 201);
    }

    /**
     * Toggle user account status.
     *
     * @param  \App\Http\Requests\Canteen\Admin\UpdateUserStatusRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(UpdateUserStatusRequest $request, $id)
    {
        $user = User::query()->findOrFail($id);

        $user->forceFill([
            'account_status' => $request->boolean('account_status'),
        ])->save();

        return response()->json([
            'message' => 'User status updated successfully.',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * @return string
     */
    private function generateVisitorCode()
    {
        do {
            $code = 'VIS' . now()->format('Ymd') . Str::upper(Str::random(4));
        } while (Visitor::query()->where('visitor_code', $code)->exists());

        return $code;
    }
}
