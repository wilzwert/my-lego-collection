<?php

namespace App\CollectionManagement\Domain\Model;

use ArrayObject;

/**
 * @author W. Zwertvaegher
 * Collection of EnrichedSet which encapsulate an object implementing Set and metadata
 * such as current status if current user has already added the Set to their collection
 * @see EnrichedSet
 */

final class EnrichedSetCollection implements \IteratorAggregate, \Countable
{
    /** @var EnrichedSet[] */
    private array $sets;

    /**
     * @param EnrichedSet[] $sets
     */
    public function __construct(array $sets = [])
    {
        foreach ($sets as $set) {
            if (!$set instanceof EnrichedSet) {
                throw new \InvalidArgumentException('All elements must be EnrichedSet');
            }
        }
        $this->sets = $sets;
    }

    public function add(EnrichedSet $set): void
    {
        $this->sets[] = $set;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->sets);
    }

    /**
     * @return EnrichedSet[]
     */
    public function toArray(): array
    {
        return $this->sets;
    }

    public function count(): int
    {
        return count($this->sets);
    }
}
