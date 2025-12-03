<?php

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Port\EmailAvailabilityChecker;
use App\Auth\Domain\Port\IdentityAvailabilityChecker;
use App\Auth\Domain\Port\PasswordHasher;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Model\EntityId;

readonly class DefaultIdentityService implements IdentityService
{
    public function __construct(
        private PasswordHasher $passwordHasher,
        private IdentityAvailabilityChecker $identityAvailabilityChecker,
        private EmailAvailabilityChecker $emailAvailabilityChecker
    ) {
    }

    public function createIdentity(string $email, string $username, string $password): ?Identity
    {
        if (!$this->identityAvailabilityChecker->isIdentityAvailable($email, $username)) {
            throw new EntityAlreadyExistsException('Identity already exists');
        }
        return Identity::create(EntityId::generate(), $email, $username, $this->passwordHasher->hash($password));
    }

    public function changeEmail(Identity $identity, string $email): Identity
    {
        if ($identity->getEmail() === $email) {
            return $identity;
        }

        if (!$this->emailAvailabilityChecker->isEmailAvailable($email)) {
            throw new EntityAlreadyExistsException('Email is taken');
        }

        return $identity->changeEmail($email);
    }

    public function complete(Identity $identity): Identity
    {
        if ($identity->isComplete()) {
            return $identity;
        }

        return $identity->complete();
    }
}
