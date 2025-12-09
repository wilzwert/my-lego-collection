<?php

namespace App\CollectionManagement\Application\Handler;

use App\CollectionManagement\Application\Service\UpdateUserElementsService;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetElement;
use App\CollectionManagement\Domain\Model\Local\UserSetStatus;
use App\CollectionManagement\Domain\Port\Driven\SetElementRepository;
use App\CollectionManagement\Domain\Port\Driven\UserSetElementRepository;
use App\CollectionManagement\Domain\Port\Driven\UserSetRepository;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Port\Driven\EventBus;
use App\Shared\Domain\Port\Driven\TransactionProvider;
use MyLegoCollection\SharedContracts\Command\CompleteUserSetCommand;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class CompleteUserSetHandler
{
    public function __construct(
        private UserSetRepository $userSetRepository,
        private SetElementRepository $setElementRepository,
        private UserSetElementRepository $userSetElementRepository,
        private UpdateUserElementsService $updateUserElementsService,
        private EventBus $eventBus,
        private TransactionProvider $transactionProvider,
    ) {
    }

    public function __invoke(CompleteUserSetCommand $command): void
    {
        // retrieve the UserSet and check its creation status
        $userSet = $this->userSetRepository->findById(EntityId::fromString($command->getUserSetId()));
        if (null === $userSet) {
            throw new EntityNotFoundException('User set not found');
        }

        // nothing to do if set has already been completed
        if (UserSetCreationStatus::COMPLETED === $userSet->getCreationStatus()) {
            return;
        }

        if (UserSetStatus::WANTED === $userSet->getStatus()) {
            throw new \InvalidArgumentException('User set should not be completed if it is not owned');
        }

        $this->transactionProvider->transactional(function () use ($userSet) {
            // retrieve the SetElements for the Set
            // and create appropriate UserSetElements
            $setElements = $this->setElementRepository->findBySetId($userSet->getSetId());
            $userSetElements = [];
            foreach ($setElements as $setElement) {
                $userSetElements[] = UserSetElement::create($userSet->getId(), $setElement->getElementId(), $setElement->getCount(), $setElement->getSpareCount());
            }
            $this->userSetElementRepository->saveAll($userSetElements);

            $userSet = $userSet->complete();
            $this->userSetRepository->save($userSet);

            // we should create/update UserElements for the $setElements
            $this->updateUserElementsService->updateAll($userSet->getUserId(), $setElements);

            $this->eventBus->dispatchAll($userSet);
        });
    }

}
