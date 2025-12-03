<?php

namespace App\Auth\Application\Handler;

use App\Auth\Application\Command\GetIdentityQuery;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;

readonly class UserCreatedHandler
{
    public function __construct(
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

        if ($identity->isComplete()) {
            return;
        }

        $this->transactionProvider->transactional(function () use ($identity) {
            $identity = $identity->complete();
            $this->identityRepository->save($identity);
            $this->eventBus->dispatchAll($identity);
            return $identity;
        });
    }
}
