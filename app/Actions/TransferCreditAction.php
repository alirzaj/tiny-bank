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
        protected MarkTransactionAsCompletedAction $markTransactionAsCompletedAction
    )
    {
    }

    public function __invoke(Card $senderCard, Card $receiverCard, int $amount): Transaction
    {
        $this->ensureSufficientBalance($senderCard, $amount);

        $transaction = ($this->storePendingTransactionAction)($senderCard, $receiverCard, $amount);

        DB::transaction(function() use ($receiverCard, $senderCard, $transaction, $amount) {
            $senderAccount = $senderCard->account()->lockForUpdate()->firstOrFail();
            $receiverAccount = $receiverCard->account()->lockForUpdate()->firstOrFail();

            $senderAccount->decrement($amount + config('fee.amount'));
            $receiverAccount->increment($amount);

            ($this->markTransactionAsCompletedAction)($transaction);
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
