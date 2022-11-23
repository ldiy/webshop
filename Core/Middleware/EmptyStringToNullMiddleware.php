<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\RequestHandlerInterface;
use Core\Http\Response;

class EmptyStringToNullMiddleware implements MiddlewareInterface
{

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $attributes = $request->getAttributes();
        foreach ($attributes as $key => $value) {
            if ($value === '') {
                $request->setAttribute($key, null);
            }
        }

        return $handler->handle($request);
    }
}