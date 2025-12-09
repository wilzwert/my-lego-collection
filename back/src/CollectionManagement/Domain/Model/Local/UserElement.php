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

    public function add(int $setCount, int $spareCount): self
    {
        return new self(
            $this->id,
            $this->userId,
            $this->elementId,
            $setCount,
            $spareCount
        );
    }
}
