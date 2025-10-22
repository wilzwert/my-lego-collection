<?php

namespace App\User\Domain\Service;

use App\User\Domain\Entity\User;

interface PasswordHasher
{
    public function hash(string $plainPassword): string;

    public function isValid(string $plainPassword, string $hashedPassword): bool;
}
