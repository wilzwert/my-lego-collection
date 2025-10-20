<?php

namespace App\CollectionManagement\Domain\Model\Local;

class Part
{
    public function __construct(
        private string $legoId,
        private string $externalId,
        private string $name,
        private string $imagePath
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

    public function getImagePath(): string
    {
        return $this->imagePath;
    }
}
