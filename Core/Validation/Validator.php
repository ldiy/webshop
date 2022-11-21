<?php

namespace Core\Validation;

use Core\Database\DB;
use Core\Exceptions\ValidationException;
use Core\Exceptions\ValidationRuleException;
use Core\Http\UploadedFile;

class Validator
{
    /**
     * The validation rules to use for each field.
     *
     * @var array
     */
    private array $rules = [];

    /**
     * The errors for each field.
     *
     * @var array
     */
    private array $errors = [];

    /**
     * The fields. The key for the rules must match this keys.
     *
     * @var array
     */
    private array $data = [];


    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Validate all the fields.
     *
     * @throws ValidationException
     */
    public function validate(): void
    {
        // Run through each rule and validated the matched field by running its value through the rules
        foreach ($this->rules as $field => $rule) {
            $value = $this->data[$field] ?? null;
            $rule->validate($value);
            if ($rule->hasError()) {
                $this->errors[$field] = $rule->getError();
            }
        }

        if (!empty($this->errors)) {
            throw new ValidationException($this);
        }
    }

    /**
     * Get the errors for all the fields.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Required validator.
     * The field must be present (not null).
     *
     * @param $value
     * @return bool
     * @throws ValidationRuleException
     */
    public static function required($value): bool
    {
        if (is_null($value)) {
            throw new ValidationRuleException('This field is required');
        }

        return true;
    }

    /**
     * Not empty validator.
     * The field must be present and not be empty.
     *
     * @param $value
     * @return bool
     * @throws ValidationRuleException
     */
    public static function notEmpty($value): bool
    {
        if (empty($value)) {
            throw new ValidationRuleException('This field is required');
        }

        return true;
    }

    /**
     * Email validator.
     * The field must be a valid email address.
     *
     * @param $value
     * @return bool
     * @throws ValidationRuleException
     */
    public static function email($value): bool
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationRuleException('This field must be a valid email address');
        }

        return true;
    }

    /**
     * Numeric validator.
     * The field must be number or a numeric string.
     *
     * @param $value
     * @return bool
     * @throws ValidationRuleException
     */
    public static function numeric($value): bool
    {
        if (!is_numeric($value)) {
            throw new ValidationRuleException('This field must be numeric');
        }

        return true;
    }

    /**
     * Minimum value validator.
     * The value of the field must be greater than the given minimum.
     *
     * @param $value
     * @param int $min
     * @return bool
     * @throws ValidationRuleException
     */
    public static function minValue($value, int $min): bool
    {
        if ($value < $min) {
            throw new ValidationRuleException("This field must be greater than {$min}");
        }

        return true;
    }

    /**
     * Maximum value validator.
     * The value of the field must be smaller than the given maximum
     *
     * @param $value
     * @param int $max
     * @return bool
     * @throws ValidationRuleException
     */
    public static function maxValue($value, int $max): bool
    {
        if ($value > $max) {
            throw new ValidationRuleException("This field must be less than {$max}");
        }

        return true;
    }

    /**
     * Minimum length validator.
     * The number of characters must be greater than the given minimum.
     *
     * @param $value
     * @param int $min
     * @return bool
     * @throws ValidationRuleException
     */
    public static function minLength($value, int $min): bool
    {
        if (strlen($value) < $min) {
            throw new ValidationRuleException("This field must be at least {$min} characters long");
        }

        return true;
    }

    /**
     * Maximum length validator.
     * The number of characters must be smaller than the given maximum.
     *
     * @param $value
     * @param int $max
     * @return bool
     * @throws ValidationRuleException
     */
    public static function maxLength($value, int $max): bool
    {
        if (strlen($value) > $max) {
            throw new ValidationRuleException("This field must be less than {$max} characters long");
        }

        return true;
    }

    /**
     * Exists validator.
     * The value must already exist in the given table and column.
     *
     * @param $value
     * @param string $table
     * @param string $column
     * @return bool
     * @throws ValidationRuleException
     */
    public static function exists($value, string $table, string $column): bool
    {
        $result =  DB::table($table)->where($column, '=', $value)->first();

        if (!$result) {
            throw new ValidationRuleException("This {$value} does not exist");
        }

        return true;
    }

    /**
     * Unique validator.
     * The value can't already exist in the given table and column
     *
     * @param $value
     * @param string $table
     * @param string $column
     * @return bool
     * @throws ValidationRuleException
     */
    public static function unique($value, string $table, string $column): bool
    {
        $result =  DB::table($table)->where($column, '=', $value)->first();

        if ($result) {
            throw new ValidationRuleException("This {$column} already exists");
        }

        return true;
    }

    /**
     * Array validator.
     * The value must be an array.
     * Each item in the array can be validated by passing a rule.
     *
     * @param $value
     * @param RuleBuilder|null $rule
     * @return bool
     * @throws ValidationRuleException
     */
    public static function isArray($value, ?RuleBuilder $rule = null): bool
    {
        if (!is_array($value)) {
            throw new ValidationRuleException("This field must be an array");
        }

        if ($rule) {
            foreach ($value as $item) {
                $rule->validate($item);
                if ($rule->hasError()) {
                    throw new ValidationRuleException($rule->getError());
                }
            }
        }

        return true;
    }

    /**
     * File validator.
     * The value must be a file.
     *
     * @param $value
     * @return bool
     * @throws ValidationRuleException
     */
    public static function file($value): bool
    {
        if (!($value instanceof UploadedFile) || !$value->isValid()) {
            throw new ValidationRuleException("This field must be a file");
        }

        return true;
    }

    /**
     * Image validator.
     * The value must be an image.
     *
     * @param $value
     * @return bool
     * @throws ValidationRuleException
     */
    public static function image($value): bool
    {
        if (!exif_imagetype($value->getPathname())) {
            throw new ValidationRuleException("This field must be an image");
        }

        return true;
    }

    /**
     * Max number of digits validator..
     *
     * @param $value
     * @param int $max
     * @return bool
     * @throws ValidationRuleException
     */
    public static function maxDigits($value, int $max): bool
    {
        // TODO: check for decimal point
        if (strlen($value) > $max) {
            throw new ValidationRuleException("This field must not be more than {$max} digits long");
        }

        return true;
    }

    /**
     * In array validator.
     * The value must be in the given array.
     *
     * @param $value
     * @param array $values
     * @return bool
     * @throws ValidationRuleException
     */
    public static function inArray($value, array $values): bool
    {
        if (!in_array($value, $values)) {
            throw new ValidationRuleException("This field must be one of the following: " . implode(', ', $values));
        }

        return true;
    }
}