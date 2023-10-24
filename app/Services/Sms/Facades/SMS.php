<?php

namespace App\Services\Sms\Facades;

use App\Services\Sms\Providers\SmsFake;
use App\Services\Sms\Providers\SmsProviderManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void send(string $phone, string $message);
 * @method static void assertSentTo(string $phone, string $message = null);
 */
class SMS extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SmsProviderManager::class;
    }

    /**
     * Replace the bound instance with a fake.
     */
    public static function fake() : SmsFake
    {
        return tap(new SmsFake(), function ($fake) {
            static::swap($fake);
        });
    }
}
