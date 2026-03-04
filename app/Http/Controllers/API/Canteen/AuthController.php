<?php

namespace App\Http\Controllers\API\Canteen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Canteen\LoginRequest;
use App\Http\Resources\Canteen\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login with staff code and password.
     *
     * @param  \App\Http\Requests\Canteen\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::query()
            ->where('staff_code', $request->input('staff_code'))
            ->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 422);
        }

        if (!$user->account_status) {
            return response()->json([
                'message' => 'Account is disabled.'
            ], 403);
        }

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        $tokenName = $request->input('device_name', 'canteen-web');
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Logout current token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = request()->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
