<?php

namespace App\Services\Sms\Providers;

use App\Services\Sms\Contracts\SmsProviderContract;
use Illuminate\Support\Facades\Http;

class Kavenegar implements SmsProviderContract
{
    public function send(string $phone, string $message): void
    {
        Http::throw()
            ->baseUrl(
                config('notification.sms.providers.ghasedak.base_url')
                . '/'
                . config('notification.sms.providers.kavenegar.api_key')
            )
            ->get(
                'sms/send.json', [
                    'receptor' => $phone,
                    'message' => $message
                ]
            );
    }
}
