<?php

namespace App\Http\Requests;

use App\Models\Card;
use App\Rules\DifferentCardNumbers;
use App\Rules\IranianCardNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sender_card_number' => [
                'required',
                new IranianCardNumberRule(),
                Rule::exists(Card::class, 'number')
            ],
            'receiver_card_number' => [
                'required',
                new IranianCardNumberRule(),
                new DifferentCardNumbers(),
                Rule::exists(Card::class, 'number')
            ],
            'amount' => ['required', 'integer', 'min:10000', 'max:50000000']
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => __('transfer.amount.min'),
            'amount.max' => __('transfer.amount.max'),
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'sender_card_number' => convert_numbers_to_english($this->input('sender_card_number')),
            'receiver_card_number' => convert_numbers_to_english($this->input('receiver_card_number')),
            'amount' => convert_numbers_to_english($this->input('amount')),
        ]);
    }
}
