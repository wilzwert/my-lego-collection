<?php

namespace App\Shared\Domain\Model;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * @author Wilhelm Zwertvaegher
 * Generic collection
 * @template T
 * @implements IteratorAggregate<int, T>
 */
class Collection implements IteratorAggregate, Countable
{

    private string $className;

    /** @var T[] */
    private array $elements;

    /**
     * @return ArrayIterator<int, T>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     *
     * @param class-string<T> $className
     * @param T[] $elements
     */
    public function __construct(string $className, array $elements = [])
    {
        foreach ($elements as $element) {
            if (!$element instanceof $className) {
                throw new InvalidArgumentException(sprintf('All elements must be %s', $className));
            }
        }
        $this->className = $className;
        $this->elements = $elements;
    }


    /**
     * @param T $element
     * @return void
     */
    public function add($element): void
    {
        if (!$element instanceof $this->className) {
            throw new InvalidArgumentException(sprintf('All elements must be %s', $this->className));
        }
        $this->elements[] = $element;
    }

    /**
     * @return T|null
     */
    public function get(int $index)
    {
        return $this->elements[$index] ?? null;
    }

    public function count(): int
    {
        // TODO: Implement count() method.
        return count($this->elements);
    }

    /**
     * @return T[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }
}
