<?php

namespace Core;

use Core\Database\DB;
use Core\Handlers\ExceptionHandler;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\MiddlewareDispatcher;
use Core\Middleware\RouterMiddleware;
use Core\Middleware\SessionMiddleware;
use Core\Routing\Router;
use Core\Session\Session;
use Core\View\Renderer;
use Throwable;

class Kernel
{
    static Kernel $instance;

    private Router $router;
    private ExceptionHandler $exceptionHandler;
    private Renderer $renderer;
    private Request $request;
    private Session $session;
    private array $configArray;


    public function __construct($config)
    {
        Kernel::$instance = $this;

        $this->configArray = $config;

        $this->request = Request::createFromGlobals();

        $this->router = new Router();

        $this->exceptionHandler = new ExceptionHandler($this->request, $this->config('debug'));

        $this->renderer = new Renderer($this->config('root_dir') . DIRECTORY_SEPARATOR . $this->config('views_dir'));

        $this->session = new Session($this->config('session'));
        $this->request->setSession($this->session);

        DB::setConfig($this->config('db'));
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @return Response
     */
    public function handleRequest(): Response
    {
        try {
            $middlewares[] = new SessionMiddleware();
            $middlewares[] =  new RouterMiddleware($this->router);
            $middlewareDispatcher = new MiddlewareDispatcher($middlewares);
            return $middlewareDispatcher->handle($this->request);
        }
        catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }

    }

    /**
     * Load the routes from the given file.
     *
     * @param $routesFile
     * @return void
     */
    public function registerRoutes($routesFile): void
    {
            $routes = require $routesFile;
            $this->router->setRoutes($routes);
    }

    /**
     * @return Renderer
     */
    public function getRenderer(): Renderer
    {
        return $this->renderer;
    }

    /**
     * Get the value of a config key.
     *
     * @param $key
     * @return mixed|null
     */
    public function config($key): mixed
    {
        if (array_key_exists($key, $this->configArray)) {
            return $this->configArray[$key];
        } else {
            return null;
        }
    }

    /**
     * @return ExceptionHandler
     */
    public function getExceptionHandler(): ExceptionHandler
    {
        return $this->exceptionHandler;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

}