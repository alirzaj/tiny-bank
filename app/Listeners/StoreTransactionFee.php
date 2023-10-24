<?php

namespace App\Listeners;

use App\Events\TransactionCompleted;

class StoreTransactionFee
{
    public function handle(TransactionCompleted $event): void
    {
        $event->transaction->fees()->create(['amount' => config('fee.amount')]);
    }
}
