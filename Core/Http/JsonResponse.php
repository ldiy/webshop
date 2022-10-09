<?php
namespace Core\Http;

class JsonResponse extends Response
{
    public function __construct(mixed $data = null, int $statusCode = 200, array $headers = [])
    {
        // TODO: parent constructor?
        $this->setContent($data);
        $this->statusCode = $statusCode;
        $this->setHeader('Content-Type', 'application/json');
    }

    public function setContent(mixed $data): static
    {
        $this->content = json_encode($data);

        return $this;
    }
}