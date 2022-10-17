<?php

namespace Core\Validation;

use Core\Exceptions\ValidationRuleException;

/**
 * Methods accessible form the magic __call method
 *
 * @method self required()
 * @method self email()
 * @method self numeric()
 * @method self minValue(int $min)
 * @method self maxValue(int $max)
 * @method self minLength(int $min)
 * @method self maxLength(int $max)
 * @method self unique(string $table, string $column = null)
 * @method self exists(string $table, string $column = null)
 */
class RuleBuilder
{
    /**
     * The rule chain
     *
     * @var array
     */
    private array $chain = [];

    /**
     * The field may be null
     *
     * @var bool
     */
    private bool $nullable = false;

    /**
     * The field is optional (not present or null)
     *
     * @var bool
     */
    private bool $optional = false;

    /**
     * This will contain the first error that a rule may have thrown.
     *
     * @var string
     */
    private string $error;


    /**
     * Magic method to be able to chain the validator rules without declaring a function for each one.
     *
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments)
    {
        $this->chain[] = [
            'name' => $name,
            'arguments' => $arguments
        ];

        return $this;
    }

    /**
     * Create a new instance
     *
     * @return static
     */
    public static function new(): self
    {
        return new self();
    }

    /**
     * Nullable rule.
     * The field may be null.
     *
     * @return $this
     */
    public function nullable(): self
    {
        $this->nullable = true;

        return $this;
    }

    /**
     * Optional rule.
     * The rest of the rules must only be checked if the field is present / not null
     *
     * @return $this
     */
    public function optional(): self
    {
        $this->optional = true;

        return $this;
    }

    /**
     * Run a value through this validation rule chain.
     *
     * @param Validator $validator
     * @param string|null $value
     * @return bool
     * @throws ValidationRuleException
     */
    public function validate(Validator $validator, ?string $value): bool
    {
        if (($this->nullable || $this->optional) && $value === null) {
            return true;
        }

        // Loop through all the rules and execute them for the given value
        foreach ($this->chain as $rule) {
            $method = $rule['name'];
            $arguments = $rule['arguments'];

            if (!method_exists($validator, $method)) {
                throw new ValidationRuleException("Validation rule {$method} does not exist");
            }

            try {
                $valid = $validator::$method($value ,...$arguments);

                if (!$valid) {
                    throw new ValidationRuleException("Validation rule {$method} failed");
                }
            } catch (ValidationRuleException $e) {
                $this->error = $e->getMessage();

                // Stop validation if the rule failed
                return false;
            }
        }

        return true;
    }

    /**
     * Get the first error.
     * This will return null if there are no errors
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error ?? null;
    }

    /**
     * Check if the validated rule has an error
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->error);
    }
}