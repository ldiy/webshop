<?php
namespace Core\Http;

class Request
{
    /**
     * @var string
     */
    private string $url;

    /**
     * @var mixed
     */
    private string $path;

    /**
     * @var string
     */
    private string $method;

    /**
     * @var string
     */
    private string $acceptHeader;

    /**
     * @var array
     */
    private array $acceptTypes;

    /**
     * Construct a new Request object from globals
     */
    function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
        $this->path = $_SERVER['PATH_INFO'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->acceptHeader = $_SERVER['HTTP_ACCEPT'];
        $this->acceptTypes = $this->parseAcceptHeader();
    }

    /**
     * @return static
     */
    static function createFromGlobals(): static
    {
        return new static();
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return ($this->method == 'HEAD') ? 'GET' : $this->method;
    }

    /**
     * @return bool
     */
    public function isHeadMethod(): bool
    {
        return $this->method == 'HEAD';
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     *
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'path' => $this->path,
            'method' => $this->method,
            'acceptHeader' => $this->acceptHeader,
            'acceptTypes' => $this->acceptTypes,
        ];
    }

    /**
     * Get the accepted types form the Accept header
     *
     * @return array key is the type, value is the quality
     */
    public function getAcceptTypes(): array
    {
        return $this->acceptTypes;
    }

    /**
     * Get the preferred type from the Accept header, based on the quality
     *
     * @return string
     */
    public function getPreferredAcceptType(): string
    {
        $acceptTypes = $this->acceptTypes;
        return array_key_first($acceptTypes);
    }

    /**
     * Check if the request accepts a given type
     *
     * @param string $type
     * @return bool
     */
    public function accepts(string $type): bool
    {
        $acceptTypes = $this->acceptTypes;
        if (array_key_exists($type, $acceptTypes))
            return true;
        elseif (array_key_exists('*/*', $acceptTypes))
            return true;
        else {
            $typeParts = explode('/', $type);
            if (array_key_exists($typeParts[0] . '/*', $acceptTypes))
                return true;
            else
                return false;
        }
    }

    /**
     * Check if the request accepts application/json
     *
     * @return bool
     */
    public function acceptsJson(): bool
    {
        return $this->accepts('application/json');
    }

    /**
     * Check if the request accepts text/html
     *
     * @return bool
     */
    public function acceptsHtml(): bool
    {
        return $this->accepts('text/html');
    }

    /**
     * Parse the Accept header into an array
     *
     * @return array key is the type, value is the quality
     */
    private function parseAcceptHeader(): array
    {
        $acceptTypes = [];
        $acceptHeader = $this->acceptHeader;
        $acceptHeader = str_replace(' ', '', $acceptHeader);
        $acceptHeader = explode(',', $acceptHeader);
        foreach ($acceptHeader as $acceptType) {
            $acceptType = explode(';', $acceptType);
            $acceptTypes[$acceptType[0]] = $acceptType[1] ?? 1;
        }
        arsort($acceptTypes);
        return $acceptTypes;
    }


}