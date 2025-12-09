<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\EnrichedSet;
use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\External\ExternalSetElement;
use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Port\Driven\SetRepository;
use App\CollectionManagement\Domain\Port\Driven\UserSetRepository;
use App\Shared\Domain\Model\EntityId;
use Override;

readonly class DefaultSetService implements SetService
{

    public function __construct(
        private LegoDataProvider $legoDataProvider
    ) {
    }

    public function createSet(string $externalSetId): Set
    {
        $externalSet = $this->legoDataProvider->getSet($externalSetId);

        return Set::create(
            $externalSet->getExternalId(),
            $externalSet->getLegoId(),
            $externalSet->getName(),
            $externalSet->getPartCount(),
            $externalSet->getImagePath(),
            $externalSet->getProductionYear()
        );
    }

    /**
     * @param Set $set the related set
     * @param array<ExternalSetElement> $externalSetElements the external data
     * @param array<Element> $elements the local Elements
     * @return array<SetElement>
     */
    #[Override]
    public function createSetElements(Set $set, array $externalSetElements, array $elements): array
    {
        $setElements = [];

        foreach($externalSetElements as $key => $externalSetElement) {
            $setElements[] = SetElement::create(
                $set->getId(),
                $elements[$externalSetElement->getExternalElement()->getExternalId()]->getId(),
                $externalSetElement->getQuantity(),
                $externalSetElement->getSpareQuantity()
            );
        }
        return $setElements;

    }
}
