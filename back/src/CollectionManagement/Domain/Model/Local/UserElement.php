<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping\Entity;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserElement
{
    /***
     * @param EntityId $id
     * @param EntityId $userId
     * @param EntityId $elementId
     * @param int $setCount the cumulated count of elements owned in owned or built sets
     * @param int $spareCount
     */
    public function __construct(
        private readonly EntityId              $id,
        private readonly EntityId              $userId,
        private readonly EntityId              $elementId,
        private readonly int        $setCount,
        private readonly int $spareCount
    ) {
    }

    public static function create(EntityId $userId, EntityId $elementId, int $setCount, int $spareCount): self
    {
        return new self(EntityId::generate(), $userId, $elementId, $setCount, $spareCount);
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getUserId(): EntityId
    {
        return $this->userId;
    }

    public function getElementId(): EntityId
    {
        return $this->elementId;
    }

    public function getSetCount(): int
    {
        return $this->setCount;
    }

    public function getSpareCount(): int
    {
        return $this->spareCount;
    }

    public function updateCount(int $setCount, int $spareCount): self
    {
        if ($setCount === 0 && $spareCount === 0) {
            return $this;
        }

        if ($this->setCount + $setCount < 0) {
            throw new \InvalidArgumentException('resulting setCount cannot be less than 0');
        }

        if ($this->spareCount + $spareCount < 0) {
            throw new \InvalidArgumentException('resulting spareCount cannot be less than 0');
        }

        return new self(
            $this->id,
            $this->userId,
            $this->elementId,
            $this->setCount + $setCount,
            $this->spareCount    + $spareCount
        );
    }
}
