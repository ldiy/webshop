<?php

namespace Core\Exceptions;

class ValidationRuleException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
