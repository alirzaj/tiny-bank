<?php

namespace Database\Factories;

use App\Card;
use App\Models\Account;
use App\Rules\IranianCardNumberRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'number' => $this->iranianCardNumber()
        ];
    }

    private function iranianCardNumber(): string
    {
        do {
            $cardNumber = '61043389' . substr($this->faker->unique()->creditCardNumber('Visa'), 8);
        } while (resolve(IranianCardNumberRule::class)->verifyIranianCardNumber($cardNumber) === false);

        return $cardNumber;
    }
}
