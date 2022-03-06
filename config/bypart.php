<?php


    /*
    |--------------------------------------------------------------------------
    | Bypart config
    |--------------------------------------------------------------------------
    |
    | Any custom config should be in this files, and then call in other place
    | Since we will use cache service
    |
    */

    return [
        //Mail related
        'mail_from_address' => env('MAIL_FROM_ADDRESS', 'support@byparts.com'),
        'mail_passowrd' => env('MAIL_PASSWORD'),
        //Core related
        'frontend_url' => env('FRONTEND_URL', 'http://localhost:4200'),
        'other_url' => env('OTHER_URL', 'http://localhost:4200'),
        'admin_url' => env('ADMIN_URL', 'http://localhost:8000'),
        //Twilo related
        'twilo_sid' => env('TWILIO_SID', 'AC03f2caea22e2d4d1b588a9f054a77364'),
        'twilo_token' => env('TWILIO_TOKEN', 'e0337381dd35b02887086ed06f683983'),
        'twilo_mobile_from' => env('TWILIO_MOBILE_FROM', '+18654320950')
     ];
