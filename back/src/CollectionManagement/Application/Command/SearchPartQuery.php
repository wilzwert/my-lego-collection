<?php

namespace App\CollectionManagement\Application\Command;

use App\Shared\Domain\Uuid;

final readonly class SearchPartQuery
{
    public function __construct(
        private readonly string $search,
        private readonly ?Uuid $userId = null
    )
    {}

    public function getSearch(): string
    {
        return $this->search;
    }

    public function getUserId() :?Uuid
    {
        return $this->userId;
    }
}
