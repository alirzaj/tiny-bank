<?php

namespace App\Actions;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Card;
use App\Models\Transaction;
use Exception;
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

        DB::beginTransaction();

        try {
            $senderAccount = $senderCard->account()->lockForUpdate()->firstOrFail();
            $receiverAccount = $receiverCard->account()->lockForUpdate()->firstOrFail();

            $senderAccount->decrement('balance', $amount + config('fee.amount'));
            $receiverAccount->increment('balance', $amount);

            $transaction->markAsCompleted();

            DB::commit();
        } catch (Exception) {
            DB::rollBack();

            $transaction->markAsFailed();
        }

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
