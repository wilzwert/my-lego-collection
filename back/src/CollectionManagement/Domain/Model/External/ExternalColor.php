<?php

namespace App\CollectionManagement\Domain\Model\External;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class ExternalColor
{
    public function __construct(
        private string $externalId,
        private string $legoId,
        private string $name,
        private string $rgbCode
    ) {
    }

    public function getExternalId(): int
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
    public function getRgbCode(): string
    {
        return $this->rgbCode;
    }
}
