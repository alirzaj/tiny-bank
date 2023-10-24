<?php

namespace App\Services\Sms\Contracts;

interface SmsProviderContract
{
    public function send(string $phone, string $message) : void;
}
