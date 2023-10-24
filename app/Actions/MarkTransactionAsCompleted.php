<?php

namespace App\Actions;

use App\Enums\TransactionStatus;
use App\Models\Transaction;

class MarkTransactionAsCompleted
{
    public function __invoke(Transaction $transaction): void
    {
        $transaction->update(['status' => TransactionStatus::COMPLETED]);

        $transaction->fees()->create(['amount' => config('fee.amount')]);
    }
}
