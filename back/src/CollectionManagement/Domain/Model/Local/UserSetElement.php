<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class UserSetElement
{

    public function __construct(
        private EntityId $id,
        private EntityId $userSetId,
        private EntityId $elementId,
        private int $count,
        private int $spareCount
    ) {
    }

    public static function create(EntityId $userSetId, EntityId $elementId, int $count, int $spareCount): self
    {
        return new self(EntityId::generate(), $userSetId, $elementId, $count, $spareCount);
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getUserSetId(): EntityId
    {
        return $this->userSetId;
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
