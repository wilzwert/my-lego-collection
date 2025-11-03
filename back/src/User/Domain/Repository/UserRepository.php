<?php

namespace App\User\Domain\Repository;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\Uuid;

interface UserRepository
{
    public function findByEmailOrUsername(string $email, string $username): ?Identity;

    public function findByIdentifier(string $identifier): ?Identity;

    public function findById(Uuid $uuid): ?Identity;

    public function save(Identity $user): void;
}
