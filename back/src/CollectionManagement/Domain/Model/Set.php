<?php

namespace App\CollectionManagement\Domain\Model;

use App\Shared\Domain\Uuid;

abstract readonly class Set
{
    public function __construct(
        private string $legoId,
        private string $externalId,
        private string $name,
        private string $partCount,
        private string $imagePath,
        private string $productionYear,
    )
    {}

    public function getLegoId(): string
    {
        return $this->legoId;
    }


    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProductionYear(): int
    {
        return $this->productionYear;
    }

    public function getPartCount(): int
    {
        return $this->partCount;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }
}
