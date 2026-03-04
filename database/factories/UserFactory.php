<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'staff_code' => 'F' . $this->faker->unique()->numberBetween(1000, 9999),
            'staff_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'staff_type' => $this->faker->randomElement(['jumbo', 'latam']),
            'role' => 'user',
            'account_status' => true,
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
        ];
    }
}
