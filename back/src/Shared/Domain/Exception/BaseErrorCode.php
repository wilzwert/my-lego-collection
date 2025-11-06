<?php

namespace App\Shared\Domain\Exception;

/**
 * @author Wilhelm Zwertvaegher
 */
interface BaseErrorCode
{
    public function getMessage(): string;

    public function getCode(): string;
}
