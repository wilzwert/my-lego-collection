<?php

namespace App\Auth\Domain\Port\Driven;

/**
 * @author Wilhelm Zwertvaegher
 */
interface EmailAvailabilityChecker
{
    public function isEmailAvailable(string $email): bool;

}
