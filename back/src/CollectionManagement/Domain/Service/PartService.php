<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\PartCollection;
use App\Shared\Domain\Uuid;

interface PartService
{
    /**
     * Find parts by a search string
     * The resulting array should contain a list of parts, enriched with current user info if available
     * The Service should merge external parts and current user's local parts if available
     *
     * @param string $search the string to search (set id, part of part title)
     * @param Uuid|null $userId the user's id if available
     * @return PartCollection
     */
    function findParts(string $search, ?Uuid $userId = null) : PartCollection;
}
