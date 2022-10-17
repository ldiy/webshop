<?php

namespace Core\Middleware;

use Core\Http\Request;
use Core\Http\RequestHandlerInterface;
use Core\Http\Response;
use Core\Session\Session;

class SessionMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        // Start the session
        $session = $request->session();
        $session->start();

        // Handle the request and store the response for later
        $response = $handler->handle($request);

        // Remove the flashed data from the previous request
        $session->removeOldFlashData();

        // Move the new flashed data to the old data
        $session->ageFlashData();

        // Return the response
        return $response;
    }
}