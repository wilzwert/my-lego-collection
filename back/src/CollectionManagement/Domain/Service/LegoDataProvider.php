<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Model\SetCollection;

/***
 * @author W. Zwertvaegher
 * This provider may be used to search Lego data
 * Several loader implementing LegoDataLoader may be defined by the infra and use various sources,
 * including cache, local DB, multiple external sources...
 * Result MUST be Set[], no matter the source
 * As PHP does not allow generics, we rely on custom collections to ensure types are as expected
 *
 */
class LegoDataProvider
{
    /**
     * @param LegoDataLoader[] $legoDataLoaders
     */
    public function __construct(
        private readonly array $legoDataLoaders,
    )
    {}

    public function findSets(string $search): SetCollection
    {
        // loaders may load from cache, from an external source, or any other source
        // we let the infrastructure set the optimal order
        foreach($this->legoDataLoaders as $legoDataLoader) {
            $sets = $legoDataLoader->findSets($search);
            if(!empty($sets)) {
                return $sets;
            }
        }
        return new SetCollection([]);
    }

    public function findParts(string $search): PartCollection
    {
        // loaders may load from cache, from an external source, or any other source
        // infrastructure must set them in the optimal order, e.g. cache first, then external source
        foreach($this->legoDataLoaders as $legoDataLoader) {
            $parts = $legoDataLoader->findParts($search);
            if(!empty($parts)) {
                return $parts;
            }
        }
        return new PartCollection([]);
    }
}
