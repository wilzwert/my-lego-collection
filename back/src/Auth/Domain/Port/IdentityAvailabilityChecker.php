<?php

namespace App\Auth\Domain\Port;

/**
 * @author Wilhelm Zwertvaegher
 */
interface IdentityAvailabilityChecker
{
    public function isIdentityAvailable(string $email, string $username): bool;

}
