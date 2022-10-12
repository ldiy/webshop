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

    // Database
    'db' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'test',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
];