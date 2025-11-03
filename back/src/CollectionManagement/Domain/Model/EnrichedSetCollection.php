<?php

namespace App\CollectionManagement\Domain\Model;

use App\Shared\Domain\Model\Collection;

/**
 * @author Wilhelm Zwertvaegher
 * Collection of EnrichedSet which encapsulate an object implementing BaseSet and metadata
 * such as current status if current user has already added the BaseSet to their collection
 * @see EnrichedSet
 * @extends Collection<EnrichedSet>
 */
final class EnrichedSetCollection extends Collection
{
    public function __construct(array $elements = [])
    {
        parent::__construct(EnrichedSet::class, $elements);
    }
}
