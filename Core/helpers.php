<?php

use Core\Http\JsonResponse;
use Core\Http\Response;
use Core\Kernel;
use JetBrains\PhpStorm\NoReturn;

if (!function_exists('app')) {

    /**
     * @return Kernel
     */
    function app(): Kernel
    {
        return Kernel::$instance;
    }
}

if (!function_exists('render')) {

    /**
     * @param string $view
     * @param array $params
     * @return string
     * @throws Throwable
     */
    function render(string $view, array $params = []): string
    {
       return app()->getRenderer()->render($view, $params);
    }
}

if (!function_exists('response')) {

    /**
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    function response(string $content = '', int $statusCode = 200, array $headers = []): Response
    {
        return new Response($content, $statusCode, $headers);
    }
}

if (!function_exists('jsonResponse')) {

    /**
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    function jsonResponse(mixed $data = null, int $statusCode = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $statusCode, $headers);
    }
}

if (!function_exists('view')) {

    /**
     * @param string $view
     * @param array $params
     * @param int $statusCode
     * @param array $headers
     * @return Response
     * @throws Throwable
     */
    function view(string $view, array $params = [], int $statusCode = 200, array $headers = []): Response
    {
        return new Response(render($view, $params), $statusCode, $headers);
    }
}

if (!function_exists('view_exists')) {

    /**
     * @param string $view
     * @return bool
     */
    function view_exists(string $view): bool
    {
        return app()->getRenderer()->viewExists($view);
    }
}

if (!function_exists('redirect')) {

    /**
     * @param string $url
     * @param int $statusCode
     * @return Response
     */
    function redirect(string $url, int $statusCode = 302): Response
    {
        $url = app()->config('app_url') . $url;
        return new Response('', $statusCode, ['Location' => $url]);
    }
}

if (!function_exists('url')) {

        /**
        * @param string $path
        * @return string
        */
        function url(string $path): string
        {
            return app()->config('app_url') . $path;
        }
}

if (!function_exists('dd')) {

    /**
     * @param mixed $var
     */
    function dd($var): void
    {
        $name = 'Debug';
        // TODO: Implement dd() function.
        echo "<br><pre>";
        var_export($var);
        echo "</pre><br>";
        die;
    }
}

if (!function_exists('session')) {

    /**
     * @return Core\Session\Session
     */
    function session(): Core\Session\Session
    {
        return app()->getSession();
    }
}

if (!function_exists('auth')) {

    /**
     * @return Core\Auth\Auth
     */
    function auth(): Core\Auth\Auth
    {
        return app()->getAuth();
    }
}

if (!function_exists('old')) {

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old(string $key, mixed $default = null): mixed
    {
        return session()->get('old')[$key] ?? $default;
    }
}

if (!function_exists('logger')) {

    /**
     * @return Core\Logging\Logger
     */
    function logger(): Core\Logging\Logger
    {
        return app()->getLogger();
    }
}