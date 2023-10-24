<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Services\Sms\Facades\SMS;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send(User $notifiable, Notification $notification): void
    {
        SMS::send($notifiable->phone, $notification->toSms($notifiable));
    }
}
