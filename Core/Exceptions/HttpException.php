<?php

namespace Core\Exceptions;

use Core\Http\Request;
use Throwable;

class HttpException extends \RuntimeException
{
    /**
     * Http status code
     *
     * @var int
     */
    protected int $statusCode;

    /**
     * @param int $statusCode
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(int $statusCode, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }



}