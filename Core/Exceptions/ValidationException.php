<?php

namespace Core\Exceptions;

use Core\Validation\Validator;

class ValidationException extends \Exception
{
    /**
     * The validation errors.
     *
     * @var array
     */
    private array $errors;

    /**
     * The used validator.
     *
     * @var Validator
     */
    private Validator $validator;

    /**
     * The http status code to use in the handler.
     *
     * @var int
     */
    public int $statusCode = 422;


    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
        $this->errors = $validator->getErrors();

        parent::__construct('The given data was invalid.');
    }

    /**
     * Get all the validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}