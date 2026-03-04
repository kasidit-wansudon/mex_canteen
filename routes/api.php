<?php

use App\Http\Controllers\API\Canteen\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\API\Canteen\Admin\MealPlanController as AdminMealPlanController;
use App\Http\Controllers\API\Canteen\Admin\QrValidationController;
use App\Http\Controllers\API\Canteen\Admin\ReportController as AdminReportController;
use App\Http\Controllers\API\Canteen\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\Canteen\AuthController;
use App\Http\Controllers\API\Canteen\MealPlanController;
use App\Http\Controllers\API\Canteen\ReservationController;
use App\Http\Controllers\API\Canteen\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user/profile', [UserProfileController::class, 'show']);

    Route::get('/meal-plans/daily', [MealPlanController::class, 'daily']);

    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::get('/reservations/{id}/qr', [ReservationController::class, 'qr']);
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::post('/meal-plans', [AdminMealPlanController::class, 'store']);
        Route::post('/qr-validate', [QrValidationController::class, 'validateQr']);

        Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);

        Route::get('/reports/daily', [AdminReportController::class, 'daily']);
        Route::get('/reports/monthly', [AdminReportController::class, 'monthly']);
        Route::get('/reports/weekly-grid', [AdminReportController::class, 'weeklyGrid']);

        Route::get('/users', [AdminUserController::class, 'index']);
        Route::patch('/users/{id}/status', [AdminUserController::class, 'updateStatus']);
        Route::post('/users/visitor', [AdminUserController::class, 'storeVisitor']);
    });
});
