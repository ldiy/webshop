<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\RequestHandlerInterface;
use Core\Http\Response;
use \RuntimeException;

class MiddlewareDispatcher implements \Core\Http\RequestHandlerInterface
{
    /**
     * @var array
     */
    private array $middlewares = [];

    /**
     * @var int
     */
    private int $index = 0;

    /**
     * @param array $middlewares
     */
    public function __construct(array $middlewares)
    {
        // TODO: Resolve middlewares?
        $this->middlewares = $middlewares;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $middleware = $this->middlewares[$this->index] ?? null;
        if ($middleware === null) {
            throw new RuntimeException('No middleware found');
        }

        $this->index++;

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        } elseif ($middleware instanceof RequestHandlerInterface) {
            return $middleware->handle($request);
        }elseif (is_callable($middleware)) {
            return $middleware($request, $this);
        } else {
            throw new RuntimeException('Invalid middleware');
        }
    }
}