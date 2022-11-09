<?php

namespace Core\Middleware;

use Core\Auth\Auth;
use Core\Http\Request;
use Core\Http\RequestHandlerInterface;
use Core\Http\Response;

class AuthMiddleware implements MiddlewareInterface
{

    protected Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if (!$this->auth->check()) {
            if ($request->prefersHtml()) {
                return redirect('/login'); // TODO: get named route
            }
            else {
                return jsonResponse(['error' => 'Unauthorized'], 401);
            }
        }

        return $handler->handle($request);
    }
}