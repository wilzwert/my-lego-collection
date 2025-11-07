<?php

namespace App\Shared\Domain\Exception;

use App\Shared\Domain\Validation\ValidationErrors;

/**
 * @author Wilhelm Zwertvaegher
 */
class ValidationException extends DomainException
{
    public function __construct(private ValidationErrors $errors)
    {
        parent::__construct('VALIDATION_FAILED');
    }

    public function getValidationErrors(): ValidationErrors
    {
        return $this->errors;
    }
}
