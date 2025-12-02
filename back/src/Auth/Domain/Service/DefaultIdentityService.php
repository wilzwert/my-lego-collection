<?php

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\EntityId;

readonly class DefaultIdentityService implements IdentityService
{
    public function __construct(
        private PasswordHasher $passwordHasher
    ) {
    }

    public function createIdentity(string $email, string $username, string $password): ?Identity
    {
        return Identity::create(EntityId::generate(), $email, $username, $this->passwordHasher->hash($password));
    }
}
