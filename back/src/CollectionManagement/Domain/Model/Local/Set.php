<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\CollectionManagement\Domain\Event\SetCreatedEvent;
use App\CollectionManagement\Domain\Model\BaseSet;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\ProducesDomainEvents;

/**
 * @author Wilhelm Zwertvaegher
 * A BaseSet that exists locally (i.e. is saved locally)
 */
final class Set extends BaseSet implements ProducesDomainEvents
{
    /**
     * @var array<DomainEvent>
     */
    private array $events = [];

    /**
     * @param EntityId $id
     * @param string $externalId
     * @param string $legoId
     * @param string $name
     * @param int $partCount
     * @param string $imagePath
     * @param int $productionYear
     */
    public function __construct(
        private readonly EntityId          $id,
        string                             $externalId,
        string           $legoId,
        string           $name,
        int              $partCount,
        string           $imagePath,
        int              $productionYear,
        private readonly SetCreationStatus $creationStatus
    ) {
        parent::__construct($externalId, $legoId, $name, $partCount, $imagePath, $productionYear);
    }

    public static function create(string $externalId, string $legoId, string $name, int $partCount, string $imagePath, int $productionYear): self
    {
        $new = new self(
            EntityId::generate(),
            $externalId,
            $legoId,
            $name,
            $partCount,
            $imagePath,
            $productionYear,
            SetCreationStatus::CREATED
        );
        $new->events = [new SetCreatedEvent($new)];
        return $new;
    }

    /**
     * @return EntityId
     */
    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getCreationStatus(): SetCreationStatus
    {
        return $this->creationStatus;
    }

    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
