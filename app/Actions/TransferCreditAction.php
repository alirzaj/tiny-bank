<?php

namespace App\Actions;

use App\Enums\TransactionStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Card;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\DB;

class TransferCreditAction
{
    public function __invoke(Card $senderCard, Card $receiverCard, int $amount): Transaction
    {
        $this->ensureSufficientBalance($senderCard, $amount);

        $transaction = $this->storePendingTransaction($senderCard, $receiverCard, $amount);

        DB::beginTransaction();

        try {
            $senderAccount = $senderCard->account()->lockForUpdate()->firstOrFail();
            $receiverAccount = $receiverCard->account()->lockForUpdate()->firstOrFail();

            $senderAccount->decrement('balance', $amount + config('fee.amount'));
            $receiverAccount->increment('balance', $amount);

            $transaction->markAsCompleted();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            report($exception);

            $transaction->markAsFailed();
        }

        return $transaction;
    }

    private function ensureSufficientBalance(Card $sender, int $amount): void
    {
        if ($sender->account->balance > ($amount + config('fee.amount'))) {
            return;
        }

        throw InsufficientBalanceException::withMessages([
            'sender_card_number' => __('transfer.insufficient_balance')
        ]);
    }

    private function storePendingTransaction(Card $sender, Card $receiver, int $amount): Transaction
    {
        return Transaction::query()->create([
            'sender_card_id' => $sender->id,
            'receiver_card_id' => $receiver->id,
            'amount' => $amount,
            'status' => TransactionStatus::PENDING,
        ]);
    }
}
