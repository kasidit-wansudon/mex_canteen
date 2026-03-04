<?php

namespace Database\Seeders;

use Database\Seeders\Canteen\MealPlanSeeder;
use Database\Seeders\Canteen\ReservationSeeder;
use Database\Seeders\Canteen\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            MealPlanSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
