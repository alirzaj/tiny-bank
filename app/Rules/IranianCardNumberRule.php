<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IranianCardNumberRule implements ValidationRule
{
    /**
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^\d{16}$/', $value)) {
            $fail(__('transfer.card.invalid_format'));
        }

        if (! $this->verifyIranianCardNumber($value)) {
            $fail(__('transfer.card.invalid_format'));
        }
    }

    public function verifyIranianCardNumber(string $cardNumber): bool
    {
        $sum = 0;
        for ($i = 0; $i < 16; $i++) {
            // Determine the weight (radix) for the digit (2 for even, 1 for odd)
            $radix = $i % 2 === 0 ? 2 : 1;

            $subDigit = (int)$cardNumber[$i] * $radix;

            $sum += $subDigit > 9 ? $subDigit - 9 : $subDigit;
        }

        return $sum % 10 !== 0;
    }
}
