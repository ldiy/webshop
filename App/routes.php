<?php

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use Core\Middleware\AuthMiddleware;
use Core\Routing\Route;


return [
    Route::get('index', '/', [HomeController::class, 'show']),

    // Login routes
    Route::get('login-form', '/login', [LoginController::class, 'show']),
    Route::post('login-post', '/login', [LoginController::class, 'login']),
    Route::get('logout', '/logout', [LoginController::class, 'logout']),

    // Registration routes
    Route::get('register-form', '/register', [RegisterController::class, 'show']),
    Route::post('register-post', '/register', [RegisterController::class, 'register']),
];