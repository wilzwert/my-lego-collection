<?php

namespace App\CollectionManagement\Application\Handler;

use App\CollectionManagement\Application\Service\CompleteSetService;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Port\Driven\SetRepository;
use App\CollectionManagement\Domain\Service\LegoDataProvider;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Port\Driven\EventBus;
use App\Shared\Domain\Port\Driven\TransactionProvider;
use MyLegoCollection\SharedContracts\Command\CompleteSetCommand;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class CompleteSetHandler
{
    public function __construct(
        private SetRepository       $setRepository,
        private LegoDataProvider    $legoDataProvider,
        private CompleteSetService  $completeSetService,
        private EventBus            $eventBus,
        private TransactionProvider $transactionProvider
    ) {
    }

    public function __invoke(CompleteSetCommand $command): void
    {
        // load Set and check its status
        $set = $this->setRepository->findById(EntityId::fromString($command->getSetId()));
        if (null === $set) {
            throw new EntityNotFoundException('Set not found');
        }

        // nothing to do if set has already been completed
        if (SetCreationStatus::COMPLETED === $set->getCreationStatus()) {
            return;
        }

        // retrieve set elements list properties from external source
        $properties = $this->legoDataProvider->getSetElements($set->getExternalId())->toData();

        $this->transactionProvider->transactional(function () use ($set, $properties) {

            $set = $this->completeSetService->completeSet($set, $properties);
            $this->setRepository->save($set);

            $this->eventBus->dispatchAll($set);
        });
    }
}
