<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\RequestHandlerInterface;
use Core\Http\Response;
use Core\Routing\Router;

class RouterMiddleware implements RequestHandlerInterface
{
    /**
     * @var Router
     */
    private Router $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        // Find the route for the request
        $route = $this->router->resolve($request);

        // Get all the middlewares for the route
        $routeMiddlewares = $route->getMiddlewares();

        // Add the route middleware to map the route to the controller
        $routeMiddlewares[] = new RouteMiddleware($route);

        // Create a middleware dispatcher to run the middlewares
        $dispatcher = new MiddlewareDispatcher($routeMiddlewares);

        // Run the middlewares and return the response
        return $dispatcher->handle($request);
    }


}