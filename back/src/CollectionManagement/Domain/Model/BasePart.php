<?php

namespace App\CollectionManagement\Domain\Model;

/**
 * @author Wilhelm Zwertvaegher
 * A representation of an external Part  retrieved from an external source
 */
abstract readonly class BasePart
{
    public function __construct(
        private string $externalId,
        private string $legoId,
        private string $name,
        private string $imagePath
    ) {
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getLegoId(): string
    {
        return $this->legoId;
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
