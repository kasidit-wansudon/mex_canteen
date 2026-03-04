<?php

namespace Database\Seeders\Canteen;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->updateOrCreate(
            ['staff_code' => 'A0001'],
            [
                'staff_name' => 'Admin User',
                'email' => 'admin@canteen.local',
                'staff_type' => 'latam',
                'role' => 'admin',
                'account_status' => true,
                'password' => Hash::make('password123'),
            ]
        );

        User::query()->updateOrCreate(
            ['staff_code' => 'U1001'],
            [
                'staff_name' => 'Jumbo Staff',
                'email' => 'jumbo.staff@canteen.local',
                'staff_type' => 'jumbo',
                'role' => 'user',
                'account_status' => true,
                'password' => Hash::make('password123'),
            ]
        );

        User::query()->updateOrCreate(
            ['staff_code' => 'U1002'],
            [
                'staff_name' => 'Latam Staff',
                'email' => 'latam.staff@canteen.local',
                'staff_type' => 'latam',
                'role' => 'user',
                'account_status' => true,
                'password' => Hash::make('password123'),
            ]
        );
    }
}
