<?php

namespace App\Shared\Domain\Exception;

use App\Shared\Domain\Validation\ValidationErrors;

/**
 * @author Wilhelm Zwertvaegher
 */
class ValidationException extends \Exception
{
    public function __construct(private ValidationErrors $errors)
    {
        parent::__construct('VALIDATION_FAILED');
    }

    public function getErrors(): ValidationErrors
    {
        return $this->errors;
    }
}
