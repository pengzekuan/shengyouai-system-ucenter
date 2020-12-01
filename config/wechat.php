<?php
return [
    'mini' => [
        'app_id' => env('WECHAT_MINI_PROGRAM_APP_ID', ''),
        'secret' => env('WECHAT_MINI_PROGRAM_SECRET', '')
    ],
    'official' => [
        'app_id' => env('WECHAT_OFFICIAL_PROGRAM_APP_ID', ''),
        'secret' => env('WECHAT_OFFICIAL_PROGRAM_SECRET', '')
    ]
];
