<?php
namespace Core\Routing;

class Route
{
    /**
     * @var string
     */
    private string $pattern;

    /**
     * @var string
     */
    private string $method;

    /**
     * @var array<string>
     */
    private array $callback;

    /**
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

    private function parsePattern($pattern): string
    {
        // Replace {} with regex
        $pattern = preg_replace('/\/\{(.*?)}/', '/([^/]+)', $pattern);
        // Escape slashes
        $pattern = preg_replace('/\//', '\/', $pattern);
        // Add start and end of string
        return '/^' . $pattern . '$/';
    }

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

    static public function get($path, $callback): Route
    {
        return new Route($path, 'GET', $callback);
    }

    static public function post($path, $callback): Route
    {
        return new Route($path, 'POST', $callback);
    }

    static public function put($path, $callback): Route
    {
        return new Route($path, 'PUT', $callback);
    }

    static public function delete($path, $callback): Route
    {
        return new Route($path, 'DELETE', $callback);
    }

    static public function patch($path, $callback): Route
    {
        return new Route($path, 'PATCH', $callback);
    }


}