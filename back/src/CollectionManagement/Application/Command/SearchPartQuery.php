<?php

namespace App\CollectionManagement\Application\Command;

use App\Shared\Domain\Uuid;

final readonly class SearchPartQuery
{
    public function __construct(
        private string $search,
        private ?Uuid $userId = null
    ) {
    }

    public function getSearch(): string
    {
        return $this->search;
    }

    public function getUserId() :?Uuid
    {
        return $this->userId;
    }
}
