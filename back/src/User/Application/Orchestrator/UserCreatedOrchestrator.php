<?php

namespace App\User\Application\Orchestrator;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Infrastructure\Messenger\CommandBus;
use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use App\User\Domain\Event\UserCreatedEvent;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
use MyLegoCollection\SharedEvent\Event\IdentityCreatedIntegrationEvent;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;

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
