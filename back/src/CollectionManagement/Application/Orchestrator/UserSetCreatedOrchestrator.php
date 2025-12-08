<?php

namespace App\CollectionManagement\Application\Orchestrator;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\CollectionManagement\Domain\Event\UserSetCreatedEvent;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\Shared\Infrastructure\Messenger\CommandBus;
use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use MyLegoCollection\SharedContracts\Command\CompleteUserSetCommand;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class UserSetCreatedOrchestrator
{
    public function __construct(
        private CommandBus          $commandBus
    ) {
    }

    public function __invoke(UserSetCreatedEvent $event): void
    {
        $userSet = $event->getUserSet();
        $set = $userSet->getSet();

        // we only dispatch the command to complete an incomplete UserSet if the related Set is already completed
        // otherwise it would result in an attempt at completing the UserSet without needed data
        // in case the set is not yet completed, the CompleteUserSetCommand will be dispatched by an orchestrator
        // when it is actually completed
        // we do not need to dispatch the CompleteSetCommand here because it is done by the dedicated SetCreatedOrchestrator
        if (
            UserSetCreationStatus::CREATED === $userSet->getCreationStatus() &&
            SetCreationStatus::COMPLETED === $set->getCreationStatus()
        ) {
            $this->commandBus->dispatch(new CompleteUserSetCommand($userSet->getId()));
        }
    }
}
