<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'biometric' => [
        'url' => env('BIOMETRIC_API_URL', 'http://103.25.129.247/prac1111/practice/practice2/get_today_data_api_new.php'),
        'override' => [
            'enabled' => env('ATTENDANCE_OVERRIDE_ENABLED', true),
            'target_employee_id' => env('ATTENDANCE_OVERRIDE_EMPLOYEE_ID', 'I2K2-0340'),
            'target_card_no' => env('ATTENDANCE_OVERRIDE_CARD_NO', '1234'),
        ],
    ],

];
