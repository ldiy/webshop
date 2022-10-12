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
// All exceptions should be already handled by the Kernel
// But if something goes terribly wrong, we can still catch it here
set_exception_handler(function (Throwable $e) {
    echo '500: Internal Server Error';
    die();
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    echo '500: Internal Server Error';
    die();
});

// Get the config variables from the config file
$config = require_once 'App/config.php';

// Create the kernel
$kernel = new Kernel($config);
$kernel->registerRoutes('App/routes.php');
$kernel->handleRequest()->send();