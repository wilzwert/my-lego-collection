<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\External\ExternalElementCollection;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\External\ExternalSetElementCollection;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Model\SetCollection;

interface LegoDataLoader
{
    /**
     * Find sets for a given search string
     * @param string $search
     * @return SetCollection|null
     */
    public function findSets(string $search): ?SetCollection;

    public function getSet(string $externalSetId): ?ExternalSet;

    /**
     * Find parts for a given search string
     * @param string $search
     * @return PartCollection|null
     */
    public function findParts(string $search): ?PartCollection;

    /**
     * Retrieve a part list for a given set
     * @param string $setExternalId
     * @return ExternalSetElementCollection|null
     */
    public function getSetParts(string $setExternalId): ?ExternalSetElementCollection;

    public function getPartElements(string $partExternalId): ?ExternalElementCollection;

    public function getSetElements(string $setExternalId): ?ExternalSetElementCollection;
}
