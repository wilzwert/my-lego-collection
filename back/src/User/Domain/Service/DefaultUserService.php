<?php

namespace App\User\Domain\Service;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\RetrieveUserForIdentity;

readonly class DefaultUserService implements UserService
{
    public function __construct(
        private RetrieveUserForIdentity $retrieveUserForIdentity
    ) {
    }

    public function createUser(EntityId $identityId): ?User
    {
        $user = $this->retrieveUserForIdentity->retrieveUser($identityId);
        if ($user) {
            return $user;
        }

        return User::create(EntityId::generate(), $identityId, new \DateTimeImmutable(), new \DateTimeImmutable());
    }
}
