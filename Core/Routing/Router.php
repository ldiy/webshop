<?php
namespace Core\Routing;

use Core\Http\Request;

class Router
{
    /**
     * @var array<Route>
     */
    private array $routes = [];

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var Route
     */
    private Route $matchedRoute;

    /**
     * @var array<string>
     */
    private array $matchedParams = [];

    /**
     * @var array
     */
    private array $matchedController;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $name
     * @param $path
     * @param $method
     * @param $callback
     * @return void
     */
    public function addRoute($name, $path, $method, $callback): void
    {
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        $this->routes[$method][$name] = new Route($name, $path, $method, $callback);
    }

    /**
     * @param $name
     * @param $path
     * @param $callback
     * @return void
     */
    public function get($name, $path, $callback): void
    {
        $this->addRoute($name, $path, 'GET', $callback);
    }

    /**
     * @param $name
     * @param $path
     * @param $callback
     * @return void
     */
    public function post($name, $path, $callback): void
    {
        $this->addRoute($name, $path, 'POST', $callback);
    }

    /**
     * @param $name
     * @param $path
     * @param $callback
     * @return void
     */
    public function put($name, $path, $callback): void
    {
        $this->addRoute($name, $path, 'PUT', $callback);
    }

    /**
     * @param $name
     * @param $path
     * @param $callback
     * @return void
     */
    public function delete($name, $path, $callback): void
    {
        $this->addRoute($name, $path, 'DELETE', $callback);
    }

    /**
     * @param $name
     * @param $path
     * @param $callback
     * @return void
     */
    public function patch($name, $path, $callback): void
    {
        $this->addRoute($name, $path, 'PATCH', $callback);
    }

    /**
     * @return bool
     */
    public function resolve(): bool
    {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();

        if ($path != '/') {
            $path = rtrim($path, '/');
        }

        foreach ($this->routes[$method] as $route) {
            if ($route->match($path)) {
                $this->matchedRoute = $route;
                $this->matchedParams = $route->findParams($path);
                $this->matchedController = Route::makeCallable($route->getCallback());
                return true;
            }
        }
        return false;
    }

    /**
     * @return Route
     */
    public function getMatchedRoute(): Route
    {
        return $this->matchedRoute;
    }

    /**
     * @return string[]
     */
    public function getMatchedParams(): array
    {
        return $this->matchedParams;
    }

    /**
     * @return array
     */
    public function getMatchedController(): array
    {
        return $this->matchedController;
    }
}