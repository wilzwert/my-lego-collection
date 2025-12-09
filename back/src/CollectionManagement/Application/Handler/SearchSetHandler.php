<?php

namespace App\CollectionManagement\Application\Handler;

use App\CollectionManagement\Application\Command\SearchSetQuery;
use App\CollectionManagement\Application\Service\FindSetsService;
use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Service\SetService;

final readonly class SearchSetHandler
{
    public function __construct(
        private FindSetsService $findSetsService
    )
    {
    }

    public function __invoke(SearchSetQuery $query): EnrichedSetCollection
    {
        return $this->findSetsService->findSets($query->getSearch(), $query->getUserId());
    }
}
