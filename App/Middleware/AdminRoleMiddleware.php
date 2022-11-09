<?php

namespace App\Middleware;

use Core\Exceptions\HttpForbiddenException;
use Core\Http\Request;
use Core\Http\RequestHandlerInterface;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;

class AdminRoleMiddleware implements MiddlewareInterface
{

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if (auth()->user()->role()->name !== 'admin') {
            throw new HttpForbiddenException('You are not authorized to access this page');
        }

        return $handler->handle($request);
    }
}