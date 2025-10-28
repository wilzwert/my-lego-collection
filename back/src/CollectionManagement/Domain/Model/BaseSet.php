<?php

namespace App\CollectionManagement\Domain\Model;

abstract readonly class BaseSet
{
    public function __construct(
        private string $externalId,
        private string $legoId,
        private string $name,
        private int $partCount,
        private string $imagePath,
        private int $productionYear,
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
