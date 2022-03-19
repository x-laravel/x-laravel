<?php

return [
    'active' => env('HTTP_LOG_ACTIVE', true),

    'except' => [
        'password',
        'password_confirmation',
    ],
];
