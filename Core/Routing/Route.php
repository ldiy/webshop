<?php
namespace Core\Routing;

class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array<string>
     */
    private $callback;


    /**
     * @param string $name
     * @param string $pattern
     * @param string $method
     * @param string[] $callback
     */
    public function __construct(string $name, string $pattern, string $method, array $callback)
    {
        $this->name = $name;
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

}