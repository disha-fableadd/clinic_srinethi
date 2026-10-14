<?php

return [
    'secret' => env('JWT_SECRET'),
    'ttl' => 60, // Token time-to-live in minutes
    'refresh_ttl' => 20160, // Refresh token time-to-live in minutes
    'algo' => 'HS256',
    'required_claims' => ['iss', 'iat', 'exp', 'nbf', 'sub', 'jti'],
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),
    'providers' => [
        'user' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],
];
