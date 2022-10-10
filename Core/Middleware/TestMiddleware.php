<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\RequestHandlerInterface;
use Core\Http\Response;

class TestMiddleware implements MiddlewareInterface
{
    private string $message;

    public function __construct(string $message = '')
    {
        $this->message = $message;
    }
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        echo $this->message . ": before<br>";
        $response = $handler->handle($request);
        $response->setContent($response->getContent() . "<br>". $this->message . ' :after');
        return $response;
    }
}
