<?php
namespace Core\Routing;

class Route
{
    /**
     * The pattern to match the request URI against.
     *
     * @var string
     */
    private string $pattern;

    /**
     * The HTTP method to match the request against.
     *
     * @var string
     */
    private string $method;

    /**
     * The controller to call when the route is matched.
     *
     * @var array<string>
     */
    private array $callback;

    /**
     * The middlewares that need to be called before the controller.
     *
     * @var array
     */
    private array $middlewares = [];


    /**
     * @param string $pattern
     * @param string $method
     * @param string[] $callback
     */
    public function __construct(string $pattern, string $method, array $callback)
    {
        if ($pattern !== '/') {
            $pattern = rtrim($pattern, '/');
        }

        $this->pattern = $this->parsePattern($pattern);
        $this->method = $method;
        $this->callback = $callback;
    }

    /**
     * @param $pattern
     * @return string
     */
    private function parsePattern($pattern): string
    {
        // Replace {} with regex
        $pattern = preg_replace('/\/\{(.*?)}/', '/([^/]+)', $pattern);
        // Escape slashes
        $pattern = preg_replace('/\//', '\/', $pattern);
        // Add start and end of string
        return '/^' . $pattern . '$/';
    }

    /**
     * @param $path
     * @return array
     */
    public function findParams($path): array
    {
        $params = [];

        if ($path != '/') {
            $path = rtrim($path, '/');
        }

        preg_match($this->pattern, $path, $params);
        array_shift($params);
        return $params;
    }

    /**
     * @param $callback
     * @return array
     */
    public static function makeCallable($callback) : array
    {
        if (is_array($callback)) {
            $callback[0] = new $callback[0]();
        }

        return $callback;
    }

    /**
     * @param $path
     * @return bool
     */
    public function match($path): bool
    {
        return boolval(preg_match($this->pattern, $path));
    }

    /**
     * @return string[]
     */
    public function getCallback(): array
    {
        return $this->callback;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param $middleware
     * @return $this
     */
    public function withMiddleware($middleware): self
    {
        if (is_array($middleware)) {
            $this->middlewares = array_merge($this->middlewares, $middleware);
        } else {
            $this->middlewares[] = $middleware;
        }

        return $this;
    }

    /**
     * Shorthand for creating a new GET route.
     *
     * @param $path
     * @param $callback
     * @return Route
     */
    static public function get($path, $callback): Route
    {
        return new Route($path, 'GET', $callback);
    }

    /**
     * Shorthand for creating a new POST route.
     *
     * @param $path
     * @param $callback
     * @return Route
     */
    static public function post($path, $callback): Route
    {
        return new Route($path, 'POST', $callback);
    }

    /**
     * Shorthand for creating a new PUT route.
     *
     * @param $path
     * @param $callback
     * @return Route
     */
    static public function put($path, $callback): Route
    {
        return new Route($path, 'PUT', $callback);
    }

    /**
     * Shorthand for creating a new DELETE route.
     *
     * @param $path
     * @param $callback
     * @return Route
     */
    static public function delete($path, $callback): Route
    {
        return new Route($path, 'DELETE', $callback);
    }
}