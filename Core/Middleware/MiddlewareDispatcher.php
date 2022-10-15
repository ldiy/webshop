<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\RequestHandlerInterface;
use Core\Http\Response;
use mysql_xdevapi\Exception;
use ReflectionException;
use \RuntimeException;

class MiddlewareDispatcher implements \Core\Http\RequestHandlerInterface
{
    /**
     * The middlewares to run
     *
     * @var array
     */
    private array $middlewares = [];

    /**
     * The current middleware index
     *
     * @var int
     */
    private int $index = 0;

    /**
     * @param array $middlewares
     * @throws ReflectionException
     */
    public function __construct(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ReflectionException
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

    /**
     * Add a middleware to the stack
     *
     * @param mixed $middleware
     * @return void
     * @throws ReflectionException
     */
    private function addMiddleware(mixed $middleware): void
    {
        if (is_string($middleware)) {
            $middleware = app()->getInstance($middleware);
        }

        $this->middlewares[] = $middleware;
    }
}