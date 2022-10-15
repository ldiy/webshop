<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Http\RequestHandlerInterface;

class TrimInputMiddleware implements MiddlewareInterface
{
    /**
     * Parameters that should not be cleaned
     *
     * @var array|string[]
     */
    private array $except = [
        'password',
        'password-confirmation',
        'current-password',
    ];

    /**
     * @param Request $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->clean($request);

        return $handler->handle($request);
    }

    private function clean(Request $request)
    {
        $attributes = $request->getAttributes();

        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->except)) {
                continue;
            }

            if (is_string($value)) {
                $attributes[$key] = trim($value);
            }
        }

        $request->setAttributes($attributes);
    }
}
