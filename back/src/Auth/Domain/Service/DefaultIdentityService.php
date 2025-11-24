<?php

namespace App\Auth\Domain\Service;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use App\Shared\Domain\Service\TransactionProviderException;

readonly class DefaultIdentityService implements IdentityService
{
    public function __construct(
        private IdentityRepository $identityRepository,
        private PasswordHasher $passwordHasher,
        private TransactionProvider $transactionProvider,
        private EventBus $eventBus
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
            $identity = new Identity(EntityId::generate(), $email, $username, $this->passwordHasher->hash($password));
            $this->identityRepository->save($identity);

            $this->eventBus->dispatch(new IdentityCreatedEvent($identity));
            return $identity;
        });
    }

    public function getIdentityById(EntityId $id): ?Identity
    {
        return $this->identityRepository->findById($id);
    }

    public function getIdentityByIdentifier(string $identifier): ?Identity
    {
        return $this->identityRepository->findByIdentifier($identifier);
    }
}
