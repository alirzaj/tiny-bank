<?php

namespace App\Actions;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Card;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransferCreditAction
{
    public function __construct(
        protected StorePendingTransactionAction $storePendingTransactionAction,
        protected MarkTransactionAsCompleted    $markTransactionAsCompleted
    )
    {
    }

    public function __invoke(Card $senderCard, Card $receiverCard, int $amount): Transaction
    {
        $this->ensureSufficientBalance($senderCard, $amount);

        $transaction = ($this->storePendingTransactionAction)($senderCard, $receiverCard, $amount);

        $senderAccount = $senderCard->account()->lockForUpdate()->firstOrFail();
        $receiverAccount = $receiverCard->account()->lockForUpdate()->firstOrFail();

        DB::transaction(function() use ($transaction, $amount, $receiverAccount, $senderAccount) {
            $senderAccount->decrement($amount + config('fee.amount'));
            $receiverAccount->increment($amount);

            ($this->markTransactionAsCompleted)($transaction);
        });

        return $transaction;
    }

    private function ensureSufficientBalance(Card $sender, int $amount): void
    {
        if ($sender->account->balance < ($amount + config('fee.amount'))) {
            throw InsufficientBalanceException::withMessages([
                'sender_card_number' => __('transfer.insufficient_balance')
            ]);
        }
    }
}
