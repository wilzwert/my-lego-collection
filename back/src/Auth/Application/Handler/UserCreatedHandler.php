<?php

namespace App\Auth\Application\Handler;

use App\Auth\Application\Command\GetIdentityQuery;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;

readonly class UserCreatedHandler
{
    public function __construct(
        private IdentityService $identityService,
        private EventBus $eventBus

    ) {
    }

    public function __invoke(UserCreatedIntegrationEvent $event): ?Identity
    {
        $identity = $this->identityService->generateValidationToken(EntityId::fromString($event->getEntityId()));
        $this->eventBus->dispatchAll($identity->getEvents());
    }
}
