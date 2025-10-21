<?php

namespace App\CollectionManagement\Application\Handler;

use App\CollectionManagement\Application\Command\SearchPartQuery;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Service\PartService;

final readonly class SearchPartHandler
{
    public function __construct(
        private PartService $partService
    ) {}

    public function __invoke(SearchPartQuery $query): PartCollection
    {
        return $this->partService->findParts($query->getSearch(),$query->getUserId());
    }
}
