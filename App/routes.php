<?php

use App\Controllers\IndexController;
use Core\Routing\Route;


return [
    Route::get('index', '/', [IndexController::class, 'index'])
        ->withMiddleware(new \Core\Middleware\TestMiddleware("Route"))
        ->withMiddleware(new \Core\Middleware\TestMiddleware("Route2")),
    Route::get('edit', '/edit/{var1}/comments/{var2}', [IndexController::class, 'edit']),
];