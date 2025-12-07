<?php

namespace App\Auth\Application\Handler;

use App\Auth\Domain\Port\Driven\IdentityRepository;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Port\Driven\EventBus;
use App\Shared\Domain\Port\Driven\TransactionProvider;
use MyLegoCollection\SharedContracts\Event\UserCreatedIntegrationEvent;

readonly class UserCreatedHandler
{
    public function __construct(
        private IdentityService  $identityService,
        private IdentityRepository  $identityRepository,
        private TransactionProvider $transactionProvider,
        private EventBus $eventBus

    ) {
    }

    public function __invoke(UserCreatedIntegrationEvent $event): void
    {
        $identityId = EntityId::fromString($event->getEntityId());
        $identity = $this->identityRepository->findById($identityId);
        if (null === $identity) {
            throw new EntityNotFoundException("Identity with id $identityId could not be found");
        }

        $this->transactionProvider->transactional(function () use ($identity) {
            $identity = $this->identityService->complete($identity);
            $this->identityRepository->save($identity);
            $this->eventBus->dispatchAll($identity);
            return $identity;
        });
    }
}
