<?php

namespace App\Services\Sms\Providers;

use App\Services\Sms\Contracts\SmsProviderContract;
use Illuminate\Support\Facades\Http;

class Ghasedak implements SmsProviderContract
{
    public function send(string $phone, string $message): void
    {
        Http::throw()
            ->withHeader('apikey', config('notification.sms.providers.ghasedak.api_key'))
            ->baseUrl(config('notification.sms.providers.ghasedak.base_url'))
            ->post(
                'sms/send/simple', [
                    'receptor' => $phone,
                    'message' => $message
                ]
            );
    }
}
