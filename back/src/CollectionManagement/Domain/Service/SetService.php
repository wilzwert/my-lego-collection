<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\Shared\Domain\Model\EntityId;

interface SetService
{
    /**
     * Find sets by a search string
     * The resulting array should contain a list of sets, enriched with current user info if available
     * The Service should merge external sets and current user's local sets if available
     *
     * @param string $search the string to search (set id, part of set title)
     * @param EntityId|null $userId the user's id if available
     * @return EnrichedSetCollection
     */
    public function findSets(string $search, ?EntityId $userId = null): EnrichedSetCollection;

    public function createSet(string $externalSetId): Set;
}
