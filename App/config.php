<?php
return [
    // Root directory of the project relative to this file
    'root_dir' => dirname( __FILE__ ) . '/../',

    // Views directory relative to root directory
    'views_dir' => 'App/Views',

    // Environment
    'env' => 'dev',

    // Debug mode
    'debug' => true,

    // Session
    'session' => [
        'cookie_name' => 'session',
        'path' => null,
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'lax',
        'lifetime' => 120,
    ],
];