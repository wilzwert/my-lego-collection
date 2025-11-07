<?php

namespace App\Shared\Domain\Validation;

use stdClass;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * A collection of validation errors
 * @see ValidationError
 *
 * @author Wilhelm Zwertvaegher
 *
 */
class ValidationErrors
{
    /**
     * A list of errors, with a field as key
     * The values are a list of ValidationError with an ErrorCode as key and a built ValidationError as value
     * @var array<string, array<string, ValidationError>> $errors
     *
     */
    private array $errors = [];

    public function add(ValidationError $error): void
    {
        if (!isset($this->errors[$error->field()])) {
            $this->errors[$error->field()] = [];
        }
        $this->addValidationError($error);
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * @return array<string, array<string, ValidationError>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function addValidationError(ValidationError $error): void
    {
        // merge the ValidationError to keep details if provided
        $lookupField = $error->field();
        $lookupCode = $error->code()->getCode();

        if (isset($this->errors[$lookupField][$lookupCode])) {
            $this->errors[$lookupField][$lookupCode]->merge($error);
        } else {
            // we create a new ValidationError to prevent original $error to be modified
            // if it is merged with other ValidationError in the future
            $addedError = new ValidationError($error->field(), $error->code(), $error->details());
            $this->errors[$lookupField][$lookupCode] = $addedError;
        }
    }

    public function merge(ValidationErrors $validationErrors): void
    {
        foreach ($validationErrors->getErrors() as $field => $errors) {
            if (!isset($this->errors[$field])) {
                $this->errors[$field] = [];
            }
            foreach ($errors as $error) {
                $this->addValidationError($error);
            }
        }
    }


    public function __toString(): string
    {
        $result = 'ValidationErrors{errors=';
        foreach ($this->errors as $field => $errors) {
            $result .= '{field='.$field.', details={';

            foreach ($errors as $code => $error) {
                $result .= '{code:'.$code.', details:{';
                foreach ($error->details() as $key => $message) {
                    $result .= '{key:'.$key.', message:{'.$message.'}}';
                }
                $result .= '}';
            }
            $result .= '}';
        }

        return $result;
    }
}
