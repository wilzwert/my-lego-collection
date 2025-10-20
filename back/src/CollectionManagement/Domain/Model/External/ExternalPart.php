<?php

namespace App\CollectionManagement\Domain\Model\External;

/**
 * @author W. Zwertvaegher
 * A representation of an external Part  retrieved from an external source
 */
final readonly class ExternalPart
{
    public function __construct(
        private string $externalId,
        private string $name,
        private string $imagePath
    )
    {}

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
