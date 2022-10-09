<?php

namespace Core\Handlers;

use Core\Exceptions\HttpException;
use Core\Http\JsonResponse;
use Core\Http\Request;
use Core\Http\Response;
use Throwable;

class ExceptionHandler
{
    private bool $displayErrors;
    private Request $request;
    private string $errorViewDir = 'Errors';    // TODO: make this configurable in conf file
    protected int $httpStatusCode = 500;

    public function __construct(Request $request, bool $displayErrors = true)
    {
        $this->request = $request;
        $this->displayErrors = $displayErrors;
    }

    /**
     * Handle an exception, and return a response based on the accepted content type
     *
     * @param Throwable $e
     * @return Response
     */
    public function handle(Throwable $e): Response
    {
        $data = $this->toArray($e);

        if ($e instanceof HttpException) {
            $this->httpStatusCode = $e->getStatusCode();
        }

        if ($this->request->acceptsHtml()) {
            return $this->handleHtml($data);
        }elseif ($this->request->acceptsJson()) {
            return $this->handleJson($data);
        }else {
            return new Response('Not acceptable', 406); // TODO: throw new HttpException(406) ?;
        }
    }

    /**
     * Get all the data for the response
     *
     * @param Throwable $e
     * @return array
     */
    public function toArray(Throwable $e): array
    {
        return [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
            'request' => $this->request->toArray(),
            'displayErrors' => $this->displayErrors,
        ];
    }

    /**
     * Render the error as html
     * Tries to use a view in the Errors directory, with the name of the http status code
     *
     * @param array $data
     * @return Response
     */
    public function handleHtml(array $data): Response
    {
        $view = $this->errorViewDir . DIRECTORY_SEPARATOR . $this->httpStatusCode;

        if (!view_exists($view)) {
            $view = $this->errorViewDir . DIRECTORY_SEPARATOR . '500';
        }

        try {
            $response = view($view, $data, $this->httpStatusCode);
        } catch (Throwable $e) {
            $response = new Response('Internal Server Error: ' . $e->getMessage(), 500);
        }

        return $response;
    }

    /**
     * Render the error as json
     *
     * @param array $data
     * @return Response
     */
    public function handleJson(array $data): Response
    {
        return new JsonResponse($data, 500);
    }
}