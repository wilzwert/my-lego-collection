<?php

namespace App\Auth\Domain\Exception;

use App\Shared\Domain\Exception\BaseErrorCode;

/**
 * @author Wilhelm Zwertvaegher
 */
enum AuthErrorCode: string implements BaseErrorCode
{
    case INVALID_USERNAME = 'Invalid username';


    public function getMessage(): string
    {
        return $this->value;
    }

    public function getCode(): string
    {
        return $this->name;
    }
}
