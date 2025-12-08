<?php

namespace App\CollectionManagement\Domain\Model;

abstract class BaseSet
{
    public function __construct(
        private readonly string $externalId,
        private readonly string $legoId,
        private readonly string $name,
        private readonly int    $partCount,
        private readonly string $imagePath,
        private readonly int    $productionYear,
    ) {
    }

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

    public function getPartCount(): int
    {
        return $this->partCount;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function getProductionYear(): int
    {
        return $this->productionYear;
    }
}
