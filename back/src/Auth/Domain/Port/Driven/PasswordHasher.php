<?php

namespace App\Auth\Domain\Port\Driven;

interface PasswordHasher
{
    public function hash(string $plainPassword): string;

    public function isValid(string $plainPassword, string $hashedPassword): bool;
}
