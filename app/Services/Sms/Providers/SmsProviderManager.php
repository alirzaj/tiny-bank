<?php

namespace App\Services\Sms\Providers;

use App\Services\Sms\Contracts\SmsProviderContract;
use Illuminate\Support\Manager;

class SmsProviderManager extends Manager
{

    public function getDefaultDriver()
    {
        return config('notification.sms.default');
    }

    public function createKavenegarDriver(): SmsProviderContract
    {

    }

    public function createGhasedakDriver(): SmsProviderContract
    {

    }
}
