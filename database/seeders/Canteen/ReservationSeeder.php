<?php

namespace Database\Seeders\Canteen;

use App\Models\MealPlan;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::query()->where('staff_code', 'U1001')->first();
        $mealPlan = MealPlan::query()->whereDate('meal_date', now()->toDateString())->where('meal_type', 'lunch')->first();

        if (!$user || !$mealPlan) {
            return;
        }

        Reservation::query()->updateOrCreate(
            [
                'meal_plan_id' => $mealPlan->id,
                'user_id' => $user->id,
            ],
            [
                'reservation_date' => $mealPlan->meal_date,
                'meal_type' => $mealPlan->meal_type,
                'reservation_type' => 'self_invitation',
                'visitor_count' => 1,
                'pickup_for_staff_code' => null,
                'meal_count' => 2,
                'qr_code_token' => 'SEED-' . Str::upper(Str::random(24)),
                'qr_expiry_time' => $mealPlan->collection_end_at ?: now()->addHours(6),
                'status' => 'confirmed',
                'remark' => 'Seed reservation',
            ]
        );
    }
}
