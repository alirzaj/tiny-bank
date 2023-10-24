<?php

return [
    'sms' => [
        'default' => env('DEFAULT_SMS_PROVIDER', 'kavenegar'),

        'providers' => [
            //TODO add doc url
            'ghasedak' => [
                'base_url' => env('GHASEDAK_BASE_URL'),
                'api_key' => env('GHASEDAK_API_KEY')
            ],

            'kavenegar' => [
                'base_url' => env('KAVENEGAR_BASE_URL'),
                'api_key' => env('KAVENEGAR_API_KEY')
            ],

            'fake' => [
                'base_url' => 'sms.url',
                'api_key' => 'abcd'
            ]
        ],
    ]
];
