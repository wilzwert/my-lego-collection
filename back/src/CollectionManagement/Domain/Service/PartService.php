<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\Local\Part;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\Shared\Domain\Model\EntityId;

interface PartService
{
    /**
     * Find parts by a search string
     * The resulting array should contain a list of parts, enriched with current user info if available
     * The Service should merge external parts and current user's local parts if available
     *
     * @param string $search the string to search (set id, part of part title)
     * @param EntityId|null $userId the user's id if available
     * @return PartCollection
     */
    public function findParts(string $search, ?EntityId $userId = null) : PartCollection;

    /**
     * @param array<string, ExternalPart> $externalParts with their externalId as key
     * @return array<string, Part> the created Parts with their externalId as key
     */
    public function getOrCreateUnknownParts(array $externalParts): array;
}
