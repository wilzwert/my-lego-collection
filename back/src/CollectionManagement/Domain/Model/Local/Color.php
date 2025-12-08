<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\ProducesDomainEvents;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class Color
{

    public function __construct(
        private EntityId $id,
        private string   $externalId,
        private string   $legoId,
        private string   $name,
        private string   $rgbCode
    ) {
    }

    public static function create(string $externalId, string $legoId, string $name, string $rgbCode): self
    {
        return new self(
            EntityId::generate(),
            $externalId,
            $legoId,
            $name,
            $rgbCode
        );
    }

    public function getId(): EntityId
    {
        return $this->id;
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

    public function getRgbCode(): string
    {
        return $this->rgbCode;
    }
}
