<?php

namespace Database\Factories;

use App\Models\Card;
use App\Transaction;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sender_card_id' => Card::factory(),
            'receiver_card_id' => Card::factory(),
            'amount' => $this->faker->numberBetween(10_000, 50_000_000),
            'status' => $this->faker->randomElement(TransactionStatus::toArray()),
        ];
    }
}
