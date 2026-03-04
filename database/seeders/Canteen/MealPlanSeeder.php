<?php

namespace Database\Seeders\Canteen;

use App\Models\MealPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class MealPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::query()->where('staff_code', 'A0001')->first();
        $startDate = now()->startOfDay();

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);

            foreach (['lunch', 'dinner'] as $mealType) {
                $isLunch = $mealType === 'lunch';

                MealPlan::query()->updateOrCreate(
                    [
                        'meal_date' => $date->toDateString(),
                        'meal_type' => $mealType,
                    ],
                    [
                        'menu_item_1' => $isLunch ? 'Steamed Rice' : 'Noodle Soup',
                        'menu_item_2' => $isLunch ? 'Grilled Chicken' : 'Braised Beef',
                        'menu_item_3' => $isLunch ? 'Vegetable Mix' : 'Green Salad',
                        'reservation_open_at' => $date->copy()->setTime(6, 0),
                        'reservation_close_at' => $isLunch
                            ? $date->copy()->setTime(10, 30)
                            : $date->copy()->setTime(16, 30),
                        'collection_start_at' => $isLunch
                            ? $date->copy()->setTime(11, 30)
                            : $date->copy()->setTime(17, 30),
                        'collection_end_at' => $isLunch
                            ? $date->copy()->setTime(14, 0)
                            : $date->copy()->setTime(20, 0),
                        'status' => 'published',
                        'created_by' => optional($admin)->id,
                        'updated_by' => optional($admin)->id,
                    ]
                );
            }
        }
    }
}
