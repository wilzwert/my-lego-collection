<?php

namespace App\CollectionManagement\Domain\Model\External;

/**
 * @author Wilhelm Zwertvaegher
 * Representation of a Part of a BaseSet retrieved from an external source
 */
final readonly class ExternalSetElement
{
    public function __construct(
        private string $externalId,
        private string $externalSetId,
        private string $externalPartId,
        private int $quantity
    )
    {}

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getExternalSetId(): string
    {
        return $this->externalSetId;
    }

    public function getExternalPartId(): string
    {
        return $this->externalPartId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
