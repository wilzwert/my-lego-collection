<?php

namespace App\CollectionManagement\Domain\Service;

class DefaultPartService implements PartService
{
    public function __construct(
        private readonly LegoDataProvider $legoDataProvider
    ) {}

    /**
     * @inheritDoc
     */
    public function findSets(string $search): array
    {
        // get parts from data provider
        $data = $this->legoDataProvider->findParts($search);
        return $data;
        // TODO enrich with user data from local db when possible
    }

}
