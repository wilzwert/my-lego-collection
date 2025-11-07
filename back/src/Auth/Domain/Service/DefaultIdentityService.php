<?php

namespace App\Auth\Domain\Service;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Model\Uuid;
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
    public function createIdentity(RegistrationCommand $command): ?Identity
    {
        return $this->transactionProvider->transactional(function () use ($command) {
            $identity = $this->identityRepository->findByEmailOrUsername($command->email, $command->username);
            if ($identity) {
                throw new EntityAlreadyExistsException('Identity already exists');
            }
            $identity = new Identity(Uuid::generate(), $command->email, $command->username, $this->passwordHasher->hash($command->password));
            $this->identityRepository->save($identity);

            $this->eventBus->dispatch(new DomainEvent('auth.identity.created', $identity->getId()));
            return $identity;
        });
    }

    public function getIdentityByIdentifier(string $identifier): ?Identity
    {
        return $this->identityRepository->findByIdentifier($identifier);
    }
}
