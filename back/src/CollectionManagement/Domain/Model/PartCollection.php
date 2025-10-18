<?php

namespace App\CollectionManagement\Domain\Model;

final class PartCollection implements \IteratorAggregate
{
    /** @var Part[] */
    private array $parts;

    /**
     * @param Part[] $parts
     */
    public function __construct(array $parts = [])
    {
        foreach ($parts as $part) {
            if (!$part instanceof Part) {
                throw new \InvalidArgumentException('All elements must be Part');
            }
        }
        $this->parts = $parts;
    }

    public function add(Part $part): void
    {
        $this->sets[] = $part;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->parts);
    }

    /**
     * @return Part[]
     */
    public function toArray(): array
    {
        return $this->parts;
    }
}
