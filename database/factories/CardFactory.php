<?php

namespace Database\Factories;

use App\Card;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'number' => $this->faker->unique()->creditCardNumber(separator: '')
        ];
    }
}
