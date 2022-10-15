<?php
namespace Core\Http;

use Core\Session\Session;
use RuntimeException;

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
     * @var string|mixed|null
     */
    private ?string $contentType = null;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var array
     */
    private array $attributes = [];

    /**
     * @var string
     */
    private string $body = '';

    /**
     * @var mixed
     */
    private mixed $parsedBody;

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

        if($this->method === 'POST') {
            $this->contentType = $_SERVER['CONTENT_TYPE'];
            $this->body = file_get_contents('php://input');
            if ($this->contentType === 'application/json') {
                $this->parsedBody = json_decode($this->body, true);
            }
            $this->attributes = $_POST;
        } else {
            $this->attributes = $_GET;
        }
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
     * Serialize the request to an array
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
            'contentType' => $this->contentType,
            'body' => $this->body,
            'attributes' => $this->attributes,
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

    /**
     * Get the session associated with this request
     *
     * @return Session
     */
    public function session(): Session
    {
        if (!$this->hasSession()) {
            throw new RuntimeException('No session associated with this request');
        }
        return $this->session;
    }

    /**
     * Check if this request has a session
     *
     * @return bool
     */
    public function hasSession(): bool
    {
        return isset($this->session);
    }

    /**
     * Set the session associated with this request
     *
     * @param Session $session
     * @return void
     */
    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

    /**
     * Get all the attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set all the attributes
     *
     * @param array $attributes
     * @return void
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * Get a request attribute (GET or POST)
     *
     * @param string $key
     * @param $default
     * @return mixed|null
     */
    public function getAttribute(string $key, $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Set a request attribute
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get the content type of the request.
     * This is only set for POST requests
     *
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * Get the body of the request
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->body;
    }

    /**
     * Get the parsed body.
     * This will be null if the content type is not application/json
     *
     * @return mixed
     */
    public function getParsedBody(): mixed
    {
        return $this->parsedBody;
    }

}