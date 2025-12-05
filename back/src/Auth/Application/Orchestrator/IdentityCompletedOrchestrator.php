<?php

namespace App\Auth\Application\Orchestrator;

use App\Auth\Domain\Event\IdentityCompletedEvent;
use App\Shared\Infrastructure\Messenger\CommandBus;
use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCompletedOrchestrator
{
    public function __construct(
        private readonly CommandBus          $commandBus
    ) {
    }

    public function __invoke(IdentityCompletedEvent $event): void
    {
        $this->commandBus->dispatch(
            new SendWelcomeNotificationCommand(
                $event->getIdentity()->getId()->value(),
                $event->getIdentity()->getValidationToken()
            )
        );
    }

}
