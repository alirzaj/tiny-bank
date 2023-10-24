<?php

namespace App\Actions;

use App\Enums\TransactionStatus;
use App\Models\Card;
use App\Models\Transaction;

class StorePendingTransactionAction
{
    public function __invoke(Card $sender, Card $receiver, int $amount): Transaction
    {
        return Transaction::query()->create([
            'sender_card_id' => $sender->id,
            'receiver_card_id' => $receiver->id,
            'amount' => $amount,
            'status' => TransactionStatus::PENDING,
        ]);
    }
}
