<?php

namespace App\CollectionManagement\Domain\Model;

use ArrayObject;
use Traversable;

/**
 * @author W. Zwertvaegher
 * Collection objects implementing Set
 * @see Set
 * @see LocalSet
 * @see ExternalSet
 */
final class SetCollection implements \IteratorAggregate, \Countable
{
    /** @var Set[] */
    private array $sets;

    /**
     * @param Set[] $sets
     */
    public function __construct(array $sets = [])
    {
        foreach ($sets as $set) {
            if (!$set instanceof Set) {
                throw new \InvalidArgumentException('All elements must be LocalSet');
            }
        }
        $this->sets = $sets;
    }

    public function add(Set $set): void
    {
        $this->sets[] = $set;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->sets);
    }

    /**
     * @return Set[]
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
