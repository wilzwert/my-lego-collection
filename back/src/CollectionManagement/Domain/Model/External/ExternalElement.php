<?php

namespace App\CollectionManagement\Domain\Model\External;

final readonly class ExternalElement
{
    public function __construct(
        private string $externalId,
        private string $legoId,
        private string $externalPartId,
        private string $imagePath,
        private string $externalColorId
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

    public function getExternalPartId(): string
    {
        return $this->externalPartId;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function getExternalColorId(): string
    {
        return $this->externalColorId;
    }
}
