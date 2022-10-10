<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\RequestHandlerInterface;
use Core\Http\Response;

interface MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler) : Response;
}