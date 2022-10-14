<?php

use App\Controllers\IndexController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use Core\Auth\Auth;
use Core\Auth\UserProvider;
use Core\Middleware\AuthMiddleware;
use Core\Routing\Route;


return [
    Route::get('index', '/', [IndexController::class, 'index'])
        ->withMiddleware(new AuthMiddleware(auth())),
    Route::get('edit', '/edit/{var1}/comments/{var2}', [IndexController::class, 'edit']),

    Route::get('login-form', '/login', [LoginController::class, 'show']),
    Route::post('login-post', '/login', [LoginController::class, 'login']),
    Route::get('logout', '/logout', [LoginController::class, 'logout']),

    Route::get('register-form', '/register', [RegisterController::class, 'show']),
    Route::post('register-post', '/register', [RegisterController::class, 'register']),
];