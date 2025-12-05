<?php

namespace App\User\Application\Orchestrator;

use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use App\User\Domain\Event\UserCreatedEvent;
use MyLegoCollection\SharedContracts\Event\UserCreatedIntegrationEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class UserCreatedOrchestrator
{
    public function __construct(
        private IntegrationEventBus $integrationBus,
    ) {
    }

    public function __invoke(UserCreatedEvent $event): void
    {
        $this->integrationBus->dispatch(
            new UserCreatedIntegrationEvent(
                $event->getUser()->getId(),
                $event->getUser()->getIdentityId(),
                $event->metadata()
            )
        );
    }

}
