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
     * The http status code to use in the handler.
     *
     * @var int
     */
    public int $statusCode = 422;


    public function __construct(Validator $validator = null)
    {
        if (!is_null($validator)) {
            $this->errors = $validator->getErrors();
        }

        parent::__construct('The given data was invalid.');
    }

    /**
     * @param array $messages
     * @return static
     */
    public static function fromMessages(array $messages): static
    {
        $self = new static;
        $self->errors = $messages;
        return $self;
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

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
}