<?php

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\TransactionProvider;
use App\Shared\Domain\Service\TransactionProviderException;

readonly class DefaultIdentityService implements IdentityService
{
    public function __construct(
        private IdentityRepository $identityRepository,
        private PasswordHasher $passwordHasher,
        private TransactionProvider $transactionProvider
    ) {
    }

    /**
     * @throws EntityAlreadyExistsException
     * @throws TransactionProviderException
     */
    public function createIdentity(string $email, string $username, string $password): ?Identity
    {
        return $this->transactionProvider->transactional(function () use ($email, $username, $password) {
            $identity = $this->identityRepository->findByEmailOrUsername($email, $username);
            if ($identity) {
                throw new EntityAlreadyExistsException('Identity already exists');
            }
            $identity = Identity::create(EntityId::generate(), $email, $username, $this->passwordHasher->hash($password));
            $this->identityRepository->save($identity);
            return $identity;
        });
    }

    public function getIdentityById(EntityId $identityId): ?Identity
    {
        return $this->identityRepository->findById($identityId);
    }

    public function getIdentityByIdentifier(string $identifier): ?Identity
    {
        return $this->identityRepository->findByIdentifier($identifier);
    }

    public function changeEmail(EntityId $identityId, string $email): Identity
    {
        $identity = $this->identityRepository->findById($identityId);
        if (null === $identity) {
            throw new EntityNotFoundException("Identity with identifier could not be found");
        }

        if ($identity->getEmail() === $email) {
            return $identity;
        }

        $existingIdentity = $this->identityRepository->findByIdentifier($email);
        if (null !== $existingIdentity) {
            throw new EntityAlreadyExistsException('Email is taken');
        }

        return $this->transactionProvider->transactional(function () use ($identity, $email) {
            $identity = $identity->changeEmail($email);
            $this->identityRepository->save($identity);
            return $identity;
        });
    }


    public function completeIdentity(EntityId $identityId): ?Identity
    {
        $identity = $this->identityRepository->findById($identityId);
        if (null === $identity) {
            throw new EntityNotFoundException("Identity with id $identityId could not be found");
        }

        if ($identity->isComplete()) {
            return $identity;
        }

        return $this->transactionProvider->transactional(function () use ($identity) {
            $identity = $identity->complete();
            $this->identityRepository->save($identity);
            return $identity;
        });
    }
}
