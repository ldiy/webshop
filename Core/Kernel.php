<?php

namespace Core;

use Core\Handlers\ExceptionHandler;
use Core\Exceptions\HttpNotFoundException;
use Core\Http\Request;
use Core\Http\Response;
use Core\Routing\Router;
use Core\View\Renderer;
use Exception;

class Kernel
{
    static Kernel $instance;

    private Router $router;
    private ExceptionHandler $exceptionHandler;
    private Renderer $renderer;
    private Request $request;
    private array $configArray;

    public function __construct($config)
    {
        Kernel::$instance = $this;

        $this->configArray = $config;

        $this->request = Request::createFromGlobals();
        $this->router = new Router($this->request);
        $this->exceptionHandler = new ExceptionHandler($this->request, $this->config('debug'));
        $this->renderer = new Renderer($this->config('root_dir') . DIRECTORY_SEPARATOR . $this->config('views_dir'));
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @return Response
     */
    public function handleRequest(): Response
    {
        try {
           return $this->handleRaw();
        }
        catch (\Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }

    }

    /**
     * Handle an incoming HTTP request. This method doesn't catch exceptions.
     *
     * @throws Exception
     */
    private function handleRaw(): Response
    {
        // Resolve the route for this request
        if(!$this->router->resolve()) {
            throw new HttpNotFoundException('No route found for this request');
        }

        // Get the found controller and parameters
        $controller = $this->router->getMatchedController();
        $params = $this->router->getMatchedParams();

        // Call the controller
        $response = $controller($this->request, ...$params);
        if (!$response instanceof Response) {
            throw new Exception('Controller did not return a response');
        }

        return $response;
    }

    /**
     * Load the routes from the given file.
     *
     * @param $routesFile
     * @return void
     */
    public function registerRoutes($routesFile): void
    {
            require $routesFile;
            loadRoutes($this->router);
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
}