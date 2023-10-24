<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Notifications\Notification;

class CreditDepositedNotification extends Notification
{
    public function __construct(public Transaction $transaction)
    {
    }

    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    public function toSms($notifiable): string
    {
        return __('notification.deposit', [
            'sender_card_number' => $this->transaction->sender->number,
            'receiver_card_number' => $this->transaction->receiver->number,
            'amount' => $this->transaction->amount,
            'transaction_id' => $this->transaction->id,
        ]);
    }
}
