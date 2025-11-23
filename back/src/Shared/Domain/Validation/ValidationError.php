<?php

namespace App\Shared\Domain\Validation;

use App\Shared\Domain\Exception\BaseErrorCode;
use App\Shared\Domain\Exception\ErrorCode;

/**
 * A validation error on a field, with a specific ErrorCode and details as an array
 * @see ErrorCode
 *
 * @author Wilhelm Zwertvaegher
 */
class ValidationError
{

    /**
     * @param String $field
     * @param BaseErrorCode $code
     * @param array<string, string|int> $details as a detailKey => message|number array
     */
    public function __construct(private readonly String $field, private readonly BaseErrorCode $code, private array $details = [])
    {
    }


    public function merge(ValidationError $error): void
    {
        if (count($error->details)) {
            $this->details = array_merge($this->details, $error->details);
        }
    }

    public function field(): string
    {
        return $this->field;
    }

    public function code(): BaseErrorCode
    {
        return $this->code;
    }

    /**
     * @return array<string, string|int>
     */
    public function details(): array
    {
        return $this->details;
    }

    public function __toString(): string
    {
        return sprintf(
            "ValidationError[code = %s, message = %s {field = %s, details = %s]",
            $this->code->getCode(),
            $this->code->getMessage(),
            $this->field,
            json_encode($this->details)
        );
    }
}
