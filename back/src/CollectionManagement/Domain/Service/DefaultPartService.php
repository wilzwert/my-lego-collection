<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\PartCollection;
use App\Shared\Domain\Model\EntityId;

readonly class DefaultPartService implements PartService
{
    public function __construct(
        private LegoDataProvider $legoDataProvider
    ) {
    }

    /**
     * @inheritDoc
     */
    public function findParts(string $search, ?EntityId $userId = null): PartCollection
    {
        // get parts from data provider
        return $this->legoDataProvider->findParts($search);
    }
}
