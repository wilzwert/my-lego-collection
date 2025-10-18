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
final class UserSetCollection implements \IteratorAggregate, \Countable
{
    /** @var UserSet[] */
    private array $userSets;

    /**
     * @param UserSet[] $userSets
     */
    public function __construct(array $userSets = [])
    {
        foreach ($userSets as $userSet) {
            if (!$userSet instanceof UserSet) {
                throw new \InvalidArgumentException('All elements must be UserSet');
            }
        }

        $this->userSets = $userSets;
    }

    public function add(UserSet $userSet): void
    {
        $this->userSets[] = $userSet;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->userSets);
    }

    /**
     * @return UserSet[]
     */
    public function toArray(): array
    {
        return $this->userSets;
    }

    public function count(): int
    {
        return count($this->userSets);
    }
}
