<?php

use App\Controllers\IndexController;
use Core\Routing\Route;


return [
    Route::get('index', '/', [IndexController::class, 'index']),
    Route::get('edit', '/edit/{var1}/comments/{var2}', [IndexController::class, 'edit']),
];