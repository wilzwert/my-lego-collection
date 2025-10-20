<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\PartCollection;
use App\Shared\Domain\Uuid;

class DefaultPartService implements PartService
{
    public function __construct(
        private readonly LegoDataProvider $legoDataProvider
    ) {}

    /**
     * @inheritDoc
     */
    public function findParts(string $search, ?Uuid $userId = null): PartCollection
    {
        // get parts from data provider
        return $this->legoDataProvider->findParts($search);
    }
}
