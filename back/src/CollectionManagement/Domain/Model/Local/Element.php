<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
class Element
{

    public function __construct(
        private readonly EntityId $id,
        private readonly EntityId $partId,
        private readonly EntityId $colorId,
        private readonly string $externalId,
        private readonly string $name,
        private string $imagePath,
    ) {
    }

    public static function create(
        EntityId $partId,
        EntityId $colorId,
        $externalId,
        string $name,
        $imagePath
    ): self {
        return new self(EntityId::generate(), $partId, $colorId, $externalId, $name, $imagePath);
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getPartId(): EntityId
    {
        return $this->partId;
    }

    public function getColorId(): EntityId
    {
        return $this->colorId;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }


}
