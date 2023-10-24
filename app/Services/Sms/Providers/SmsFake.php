<?php

namespace App\Services\Sms\Providers;

use App\Services\Sms\Contracts\SmsProviderContract;
use Tests\TestCase;

class SmsFake implements SmsProviderContract
{
    public array $sent = [];

    public function send(string $phone, string $message): void
    {
        $this->sent[$phone] = $message;
    }

    public function assertSentTo(string $phone, string $message = null) : void
    {
        TestCase::assertArrayHasKey($phone, $this->sent);

        if (filled($message)){
            TestCase::assertEquals($message, $this->sent[$phone]);
        }
    }
}
