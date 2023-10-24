<?php

namespace App\Listeners;

use App\Events\TransactionCompleted;
use App\Notifications\CreditDepositedNotification;
use App\Notifications\CreditWithdrewNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTransactionCompletedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notification';

    public function handle(TransactionCompleted $event): void
    {
        $event
            ->transaction
            ->sender
            ->account
            ->user
            ->notify(new CreditWithdrewNotification($event->transaction));

        $event
            ->transaction
            ->receiver
            ->account
            ->user
            ->notify(new CreditDepositedNotification($event->transaction));
    }
}
