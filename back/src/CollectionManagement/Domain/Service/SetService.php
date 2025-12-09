<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\External\ExternalSetElement;
use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\Shared\Domain\Model\EntityId;

interface SetService
{

    public function createSet(string $externalSetId): Set;

    /**
     * @param Set $set
     * @param array<ExternalSetElement> $externalSetElements
     * @param array<Element> $elements
     * @return array<SetElement>
     */
    public function createSetElements(Set $set, array $externalSetElements, array $elements): array;
}
