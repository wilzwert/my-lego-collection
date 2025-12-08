<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping\Entity;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class SetElement
{
    public function __construct(
        private EntityId $id,
        private EntityId $setId,
        private EntityId $elementId,
        private int $count,
        private int $spareCount
    ) {
    }

    public static function create(EntityId $setId, EntityId $elementId, int $count, int $spareCount): self
    {
        return new self(EntityId::generate(), $setId, $elementId, $count, $spareCount);
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getSetId(): EntityId
    {
        return $this->setId;
    }

    public function getElementId(): EntityId
    {
        return $this->elementId;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getSpareCount(): int
    {
        return $this->spareCount;
    }
}
