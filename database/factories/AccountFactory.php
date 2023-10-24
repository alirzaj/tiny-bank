<?php

namespace Database\Factories;

use App\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'number' => $this->faker->unique()->numerify('##########'), // Generates a unique 10-digit number
            'balance' => $this->faker->numberBetween(1000, 10000), // Example range for the balance
        ];
    }
}
