<?php

namespace Database\Factories;

use App\Fee;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'amount' => config('fee.amount')
        ];
    }
}
