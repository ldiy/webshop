<?php

namespace Core\Exceptions;

use Throwable;

class HttpForbiddenException extends HttpException
{

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct( 403, 'Forbidden: ' . $message, $code, $previous);
    }
}