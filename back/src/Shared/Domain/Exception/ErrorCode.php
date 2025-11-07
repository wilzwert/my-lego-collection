<?php

namespace App\Shared\Domain\Exception;

/**
 * @author Wilhelm Zwertvaegher
 */
enum ErrorCode: string implements BaseErrorCode
{
    case UNKNOWN_ERROR = 'Unknown error';
    case INVALID_EMAIL = 'Invalid email';
    case INVALID_URL = 'Invalid url';
    case INVALID_UUID = 'Invalid uuid';
    case FIELD_CANNOT_BE_NULL = 'The field cannot be null';
    case FIELD_CANNOT_BE_EMPTY = 'The field cannot be empty';
    case FIELD_VALUE_TOO_SMALL = 'The field value is too small';
    case FIELD_VALUE_TOO_BIG = 'The field value is too big';
    case FIELD_VALUE_TOO_SHORT = 'The field value is too short';
    case FIELD_VALUE_TOO_LONG = 'The field value is too long';

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
