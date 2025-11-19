<?php

namespace App\CollectionManagement\Application\Command;

use App\Shared\Domain\Model\EntityId;

final readonly class SearchSetQuery
{
    public function __construct(
        private string $search,
        private ?EntityId $userId = null
    )
    {}

    public function getSearch(): string
    {
        return $this->search;
    }

    public function getUserId() :?EntityId
    {
        return $this->userId;
    }
}
