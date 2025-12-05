<?php

namespace App\Auth\Application\Orchestrator;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Infrastructure\Messenger\CommandBus;
use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use MyLegoCollection\SharedContracts\Command\CreateUserCommand;
use MyLegoCollection\SharedContracts\Event\IdentityCreatedIntegrationEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class IdentityCreatedOrchestrator
{
    public function __construct(
        private CommandBus          $commandBus,
        private IntegrationEventBus $integrationBus,
    ) {
    }

    public function __invoke(IdentityCreatedEvent $event): void
    {
        // identity creation MUST trigger user creation
        $this->commandBus->dispatch(
            new CreateUserCommand(
                $event->getIdentity()->getId(),
                $event->metadata()
            ),
        );

        // an integration event should be dispatched for possible handlers
        $this->integrationBus->dispatch(
            new IdentityCreatedIntegrationEvent(
                $event->getIdentity()->getId(),
                $event->metadata()
            )
        );
    }

}
