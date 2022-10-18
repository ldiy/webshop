<?php
return [
    // Root directory of the project relative to this file
    'root_dir' => dirname( __FILE__ ) . '/../',

    'app_url' => 'http://127.0.0.1/webshop',

    // Views directory relative to root directory
    'views_dir' => 'App/Views',

    // Environment
    'env' => 'dev',

    // Debug mode
    'debug' => true,

    // Session
    'session' => [
        'cookie_name' => 'session',
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'lax',
        'lifetime' => 0,
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

    // Logging
    'logging' => [
        // Path to the log file relative to root directory
        'path' => 'storage/logs/log.txt',

        // Log level, one of: emergency, alert, critical, error, warning, notice, info, debug
        // Everything above or equal to the given level will be logged
        'level' => 'debug',
    ],
];