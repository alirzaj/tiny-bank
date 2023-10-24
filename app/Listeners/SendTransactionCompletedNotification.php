<?php

namespace App\Listeners;

use App\Events\TransactionCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTransactionCompletedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notification';

    public function handle(TransactionCompleted $event): void
    {
        //TODO implement
    }
}
