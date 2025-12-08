<?php

namespace App\CollectionManagement\Domain\Model\External;

/**
 * @author Wilhelm Zwertvaegher
 * Representation of an element of a BaseSet retrieved from an external source
 */
readonly class ExternalSetElement
{
    public function __construct(
        private string $externalSetId,
        private ExternalElement $externalElement,
        private ExternalPart $externalPart,
        private ExternalColor $externalColor,
        private int $quantity,
        private int $spareQuantity
    ) {
    }

    public function getExternalSetId(): string
    {
        return $this->externalSetId;
    }

    public function getExternalElement(): ExternalElement
    {
        return $this->externalElement;
    }

    public function getExternalPart(): ExternalPart
    {
        return $this->externalPart;
    }

    public function getExternalColor(): ExternalColor
    {
        return $this->externalColor;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getSpareQuantity(): int
    {
        return $this->spareQuantity;
    }
}
