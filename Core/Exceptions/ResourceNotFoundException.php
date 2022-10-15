<?php

namespace Core\Exceptions;

use Throwable;

class ResourceNotFoundException extends HttpNotFoundException
{
    public function __construct(string $message = 'Resource not found', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}