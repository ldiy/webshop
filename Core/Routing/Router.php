<?php
namespace Core\Routing;

use Core\Exceptions\HttpNotFoundException;
use Core\Http\Request;

class Router
{
    /**
     * @var array<Route>
     */
    private array $routes = [];


    public function __construct()
    {
    }

    /**
     * @param array $routes
     * @return void
     */
    public function setRoutes(array $routes): void
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    /**
     * @param Route $route
     * @return void
     */
    public function addRoute(Route $route): void
    {
        $this->routes[$route->getMethod()][$route->getName()] = $route;
    }


    /**
     * @param Request $request
     * @return Route
     */
    public function resolve(Request $request): Route
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        if ($path != '/') {
            $path = rtrim($path, '/');
        }

        if (!isset($this->routes[$method])) {
            throw new HttpNotFoundException('No route found for this request');
        }

        foreach ($this->routes[$method] as $route) {
            if ($route->match($path)) {
                return $route;
            }
        }

        throw new HttpNotFoundException('No route found for this request');
    }

}