<?php

namespace App\Auth\Domain\Port\Driven;

/**
 * @author Wilhelm Zwertvaegher
 */
interface IdentityAvailabilityChecker
{
    public function isIdentityAvailable(string $email, string $username): bool;

}
