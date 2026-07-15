<?php

return [

    'defaults' => [
        'guard'     => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'cashiers'),
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'cashiers',
        ],
    ],

    'providers' => [
        // ShowDrive uses the Cashier model as its sole authentication model.
        // The default User model is not used for login.
        'cashiers' => [
            'driver' => 'eloquent',
            'model'  => env('AUTH_MODEL', App\Models\Cashier::class),
        ],
    ],

    'passwords' => [
        'cashiers' => [
            'provider' => 'cashiers',
            'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
