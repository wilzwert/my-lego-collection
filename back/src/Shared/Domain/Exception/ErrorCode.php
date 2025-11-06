<?php

namespace App\Shared\Domain\Exception;

/**
 * @author Wilhelm Zwertvaegher
 */
enum ErrorCode: string implements BaseErrorCode
{
    case UNKNOWN_ERROR = 'Unknown error';
    case INVALID_EMAIL = 'Invalid email';
    case FIELD_CANNOT_BE_EMPTY = 'The field cannot be empty';
    case FIELD_VALUE_TOO_BIG = 'The field value is too big';

    case ENTITY_NOT_FOUND = 'Entity not found';

    public function getMessage(): string
    {
        return $this->value;
    }

    public function getCode(): string
    {
        return $this->name;
    }
}
