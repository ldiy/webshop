<?php

use App\Controllers\IndexController;
use Core\Routing\Route;

function loadRoutes($router): void
{
    $router->get('index', '/', [IndexController::class, 'index']);
    $router->get('edit', '/edit/{var1}/comments/{var2}', [IndexController::class, 'edit']);
}
