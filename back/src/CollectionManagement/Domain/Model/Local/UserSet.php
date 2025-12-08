<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\CollectionManagement\Domain\Event\UserSetCreatedEvent;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\ProducesDomainEvents;

/**
 * A local user's set which consists of an id and a local set
 * TODO : add meaningful information, such as status (OWNED, WANTED...), status last update, rating...
 *
 * @author Wilhelm Zwertvaegher
 */
class UserSet implements ProducesDomainEvents
{
    /**
     * @var array<DomainEvent>
     */
    private array $events = [];

    public function __construct(
        private readonly EntityId              $id,
        private readonly EntityId              $userId,
        private readonly Set                   $set,
        private readonly UserSetCreationStatus $creationStatus,
        private readonly ?UserSetStatus        $status = null
    ) {
    }

    public static function create(EntityId $userId, Set $set): self
    {
        $new = new self(EntityId::generate(), $userId, $set, UserSetCreationStatus::CREATED);
        $new->events = [new  UserSetCreatedEvent($new)];
        return $new;
    }

    public function getSet(): Set
    {
        return $this->set;
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getUserId(): EntityId
    {
        return $this->userId;
    }

    public function getCreationStatus(): UserSetCreationStatus
    {
        return $this->creationStatus;
    }

    public function getStatus(): UserSetStatus
    {
        return $this->status;
    }

    /**
     * @return array<DomainEvent>
     */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
