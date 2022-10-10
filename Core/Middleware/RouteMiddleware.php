<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Http\RequestHandlerInterface;
use Core\Routing\Route;
use \RuntimeException;

class RouteMiddleware implements RequestHandlerInterface
{
    private Route $route;

    /**
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $path = $request->getPath();

        // Get the found controller and parameters
        $controller = Route::makeCallable($this->route->getCallback());
        $params = $this->route->findParams($path);

        // Call the controller
        $response = $controller($request, ...$params);
        if (!$response instanceof Response) {
            throw new RuntimeException('Controller did not return a response');
        }

        return $response;
    }
}
