<?php

use App\Controllers\CartController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\RegisterController;
use App\Middleware\AdminRoleMiddleware;
use Core\Middleware\AuthMiddleware;
use Core\Routing\Route;


return [
    Route::get('/', [HomeController::class, 'show']),

    // Login routes
    Route::get('/login', [LoginController::class, 'show']),
    Route::post('/login', [LoginController::class, 'login']),
    Route::get('/logout', [LoginController::class, 'logout']),

    // Registration routes
    Route::get('/register', [RegisterController::class, 'show']),
    Route::post('/register', [RegisterController::class, 'register']),

    // Product routes
    Route::get('/product/{id}', [ProductController::class, 'show']),
    Route::get('/search', [ProductController::class, 'search']),

    // Category routes
    Route::get('/category/{id}', [CategoryController::class, 'show']),

    // Cart routes
    Route::get('/cart', [CartController::class, 'show'])
        ->withMiddleware(AuthMiddleware::class),
    Route::post('/cart/add', [CartController::class, 'add'])
        ->withMiddleware(AuthMiddleware::class),
    Route::post('/cart/update', [CartController::class, 'update'])
        ->withMiddleware(AuthMiddleware::class),

    // Order routes
    Route::get('/checkout', [OrderController::class, 'create'])
        ->withMiddleware(AuthMiddleware::class),


    /**
     * Admin routes
     */
    // Product routes
    Route::get('/admin/product', [ProductController::class, 'index'])
        ->withMiddleware(AuthMiddleware::class)
        ->withMiddleware(AdminRoleMiddleware::class),
    Route::get('/admin/product/{id}', [ProductController::class, 'showAdmin']) // TODO: merge with user show
        ->withMiddleware(AuthMiddleware::class)
        ->withMiddleware(AdminRoleMiddleware::class),
    Route::post('/admin/product', [ProductController::class, 'store'])
        ->withMiddleware(AuthMiddleware::class)
        ->withMiddleware(AdminRoleMiddleware::class),
    Route::post('/admin/product/{id}/update', [ProductController::class, 'update'])
        ->withMiddleware(AuthMiddleware::class)
        ->withMiddleware(AdminRoleMiddleware::class),
    Route::delete('/admin/product/{id}', [ProductController::class, 'destroy'])
        ->withMiddleware(AuthMiddleware::class)
        ->withMiddleware(AdminRoleMiddleware::class),
];