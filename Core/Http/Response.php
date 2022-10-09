<?php
namespace Core\Http;

class Response
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * @param int $statusCode
     * @param array $headers
     * @param string $content
     */
    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->content = $content;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function prepare(Request $request): static
    {
        if ($request->isHeadMethod()) {
            $this->content = '';
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function sendHeaders(): static
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function sendContent(): static
    {
        echo $this->content;
        return $this;
    }

    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param $url
     * @return void
     */
    public function redirect($url)
    {
        $this->setHeader('Location', $url);
    }

}