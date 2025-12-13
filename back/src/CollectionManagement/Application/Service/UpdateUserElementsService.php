<?php

namespace App\CollectionManagement\Application\Service;

use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Model\Local\UserElement;
use App\CollectionManagement\Domain\Port\Driven\UserElementRepository;
use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class UpdateUserElementsService
{
    public function __construct(private UserElementRepository $userElementRepository)
    {

    }

    /**
     * @param EntityId $userId
     * @param array<SetElement> $setElements
     * @return void
     */
    public function updateAll(EntityId $userId, array $setElements): void
    {
        // retrieve related SetElements ids ; they will be used to keep track of UserElements to update
        $elementsIds = array_map(fn (SetElement $setElement) => $setElement->getElementId(), $setElements);
        $setElementsByIds = array_combine($elementsIds, $setElements);

        // naive version : create UserElement that don't already exist and update the ones that already exist
        /** @var array<UserElement> $existingUserElements */
        $existingUserElements = $this->userElementRepository->findByUserIdAndElementsIds($userId, $elementsIds);
        $all = $this->userElementRepository->findAll();
        /** @var list<UserElement> $userElementsToSave */
        $userElementsToSave = [];

        foreach ($existingUserElements as $existingUserElement) {
            $elementId = $existingUserElement->getElementId()->value();
            $userElementsToSave[] = $existingUserElement->updateCount($setElementsByIds[$elementId]->getCount(), $setElementsByIds[$elementId]->getSpareCount());

            // now that the UserElement has been handled, remove the SetElement from the list
            unset($setElementsByIds[$elementId]);
        }

        // create new UserElements
        foreach ($setElementsByIds as $setElement) {
            $userElementsToSave[] = UserElement::create($userId, $setElement->getElementId(), $setElement->getCount(), $setElement->getSpareCount());
        }

        // ask the repo port to save all
        $this->userElementRepository->saveAll($userElementsToSave);
    }
}
