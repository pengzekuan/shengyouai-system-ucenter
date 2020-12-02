<?php
return [
    'wechat' => [
        'mini' => [
            'app_id' => env('WECHAT_MINI_PROGRAM_APPID', ''),
            'secret' => env('WECHAT_MINI_PROGRAM_SECRET', '')
        ],
        'official' => [
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID', ''),
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', '')
        ]
    ],
    'sms' => [
        'appId' => env('SMS_APP_ID'),
        'appKey' => env('SMS_APP_KEY'),
        'appSign' => env('SMS_APP_SIGN'),
        'tempId' => env('SMS_TEMPLATE_ID'),
        'expires_in' => env('SMS_CODE_EXPIRES_IN', 300),
    ]
];
