<?php

namespace App\Auth\Domain\Port;

/**
 * @author Wilhelm Zwertvaegher
 */
interface EmailAvailabilityChecker
{
    public function isEmailAvailable(string $email): bool;

}
