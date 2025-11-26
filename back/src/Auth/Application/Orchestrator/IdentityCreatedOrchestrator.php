<?php

namespace App\Auth\Application\Orchestrator;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Infrastructure\Messenger\CommandBus;
use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
use MyLegoCollection\SharedEvent\Event\IdentityCreatedIntegrationEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedOrchestrator
{
    public function __construct(
        private readonly CommandBus          $commandBus,
        private readonly IntegrationEventBus $integrationBus,
    ) {
    }

    public function __invoke(IdentityCreatedEvent $event): void
    {
        $this->commandBus->dispatch(
            new CreateUserCommand($event->getIdentity()->getId()),
        );

        $this->integrationBus->dispatch(
            new IdentityCreatedIntegrationEvent(
                $event->getIdentity()->getId(),
                $event->payload(),
                $event->metadata()
            )
        );
    }

}
