<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\LocalSet;
use App\CollectionManagement\Domain\Model\SetCollection;

interface SetService
{
    /**
     * Find sets by a search string
     * The resulting array should contain a list of sets, enriched with current user info if available
     *
     * @param string $search the string to search (set id, part of set title)
     * @return EnrichedSetCollection
     */
    function findSets(string $search) : EnrichedSetCollection;
}
