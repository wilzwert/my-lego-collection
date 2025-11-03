<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\Shared\Domain\Model\Uuid;

interface SetService
{
    /**
     * Find sets by a search string
     * The resulting array should contain a list of sets, enriched with current user info if available
     * The Service should merge external sets and current user's local sets if available
     *
     * @param string $search the string to search (set id, part of set title)
     * @param Uuid|null $userId the user's id if available
     * @return EnrichedSetCollection
     */
    function findSets(string $search, ?Uuid $userId = null) : EnrichedSetCollection;
}
