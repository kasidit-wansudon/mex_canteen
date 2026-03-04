<?php

namespace App\Http\Controllers\API\Canteen;

use App\Http\Controllers\Controller;
use App\Http\Resources\Canteen\UserResource;

class UserProfileController extends Controller
{
    /**
     * Return authenticated profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        return response()->json([
            'data' => new UserResource(request()->user()),
        ]);
    }
}
