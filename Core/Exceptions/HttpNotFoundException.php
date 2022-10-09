<?php
namespace Core\Exceptions;

use Core\Http\Request;
use Throwable;

class HttpNotFoundException extends HttpException
{

        /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct( 404, $message, $code, $previous);
    }
}