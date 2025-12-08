<?php

namespace App\CollectionManagement\Application\Orchestrator;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\CollectionManagement\Domain\Event\SetCompletedEvent;
use App\CollectionManagement\Domain\Event\UserSetCreatedEvent;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Port\Driven\UserSetRepository;
use App\Shared\Infrastructure\Messenger\CommandBus;
use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use MyLegoCollection\SharedContracts\Command\CompleteUserSetCommand;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class SetCompletedOrchestrator
{
    public function __construct(
        private CommandBus          $commandBus,
        private UserSetRepository   $userSetRepository
    ) {
    }

    public function __invoke(SetCompletedEvent $event): void
    {
        $set = $event->getSet();

        // dispatch CompleteUserSetCommand for each waiting UserSet
        $userSets = $this->userSetRepository->findIncompleteBySet($set);
        foreach ($userSets as $userSet) {
            $this->commandBus->dispatch(new CompleteUserSetCommand($userSet));
        }
    }
}
