<?php

namespace App\CollectionManagement\Application\Orchestrator;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\CollectionManagement\Domain\Event\UserSetCompletedEvent;
use App\CollectionManagement\Domain\Event\UserSetCreatedEvent;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetStatus;
use App\Shared\Infrastructure\Messenger\CommandBus;
use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use MyLegoCollection\SharedContracts\Command\CompleteUserSetCommand;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class UserSetCompletedOrchestrator
{
    public function __construct(
        private CommandBus          $commandBus
    ) {
    }

    public function __invoke(UserSetCompletedEvent $event): void
    {
        $userSet = $event->getUserSet();

        if (
            UserSetCreationStatus::COMPLETED === $userSet->getCreationStatus()
        ) {
            // $this->commandBus->dispatch(new CompleteUserSetCommand($userSet->getId()));
        }
    }
}
