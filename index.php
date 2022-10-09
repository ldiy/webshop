<?php

use Core\Kernel;

// Autoload classes
spl_autoload_register(function ($class_name) {
    $file = str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($file)) {
        require($file);
    }
});

// Load helper file
require_once 'Core/helpers.php';

// Set the global error and exception handlers
// This will try to use the kernels exception handler
// If it fails, it will show a simple error message
set_exception_handler(function (Throwable $e) {
    try {
        app()->getExceptionHandler()->handle($e);
    } catch (Throwable $e2) {
        echo '500: Internal Server Error';
    }
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    try {
        app()->getExceptionHandler()->handle(new ErrorException($errstr, 0, $errno, $errfile, $errline));
    } catch (Throwable $e2) {
        echo '500: Internal Server Error';
    }
});

// Get the config variables from the config file
$config = require_once 'App/config.php';

// Create the kernel
$kernel = new Kernel($config);
$kernel->registerRoutes('App/routes.php');
$kernel->handleRequest()->send();