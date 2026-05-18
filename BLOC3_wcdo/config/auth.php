<?php

return [

    'defaults' => [
        'guard'     => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'collaborateurs'),
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'collaborateurs',
        ],
    ],

    /*
     * Sprint 0 : le provider pointe sur App\Models\Collaborateur, modèle qui
     * sera créé en Sprint 1 (table collaborateurs) et rendu authentifiable
     * en Sprint 2. Conforme au CDC : pas de table users séparée.
     */
    'providers' => [
        'collaborateurs' => [
            'driver' => 'eloquent',
            'model'  => env('AUTH_MODEL', App\Models\Collaborateur::class),
        ],
    ],

    'passwords' => [
        'collaborateurs' => [
            'provider' => 'collaborateurs',
            'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
