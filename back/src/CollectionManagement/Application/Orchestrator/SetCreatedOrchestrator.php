<?php

namespace App\CollectionManagement\Application\Orchestrator;

use App\CollectionManagement\Domain\Event\SetCreatedEvent;
use App\CollectionManagement\Domain\Event\UserSetCreatedEvent;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\Shared\Infrastructure\Messenger\CommandBus;
use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use App\User\Domain\Event\UserCreatedEvent;
use MyLegoCollection\SharedContracts\Command\CompleteSetCommand;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class SetCreatedOrchestrator
{
    public function __construct(
        private CommandBus          $commandBus
    ) {
    }

    public function __invoke(SetCreatedEvent $event): void
    {
        $set = $event->getSet();

        if (SetCreationStatus::CREATED === $set->getCreationStatus()) {
            // dispatch CompleteUserSetCommand
            $this->commandBus->dispatch(new CompleteSetCommand($set->getId()));
        }
    }
}
