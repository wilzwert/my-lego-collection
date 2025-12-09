<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\CollectionManagement\Domain\Event\SetCompletedEvent;
use App\CollectionManagement\Domain\Event\UserSetCompletedEvent;
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

    private readonly int $elementsCount;


    public function __construct(
        private readonly EntityId              $id,
        private readonly EntityId              $userId,
        private readonly EntityId                   $setId,
        private readonly \DateTimeImmutable $createdAt,
        private readonly UserSetCreationStatus $creationStatus,
        private readonly UserSetStatus        $status,
        private readonly \DateTimeImmutable $statusDate
    ) {
    }

    public static function create(EntityId $userId, EntityId $setId, UserSetStatus $status): self
    {
        $new = new self(
            EntityId::generate(),
            $userId,
            $setId,
            new \DateTimeImmutable(),
            UserSetCreationStatus::CREATED,
            $status,
            new \DateTimeImmutable()
        );
        $new->events = [new  UserSetCreatedEvent($new)];
        return $new;
    }

    public function getSetId(): EntityId
    {
        return $this->setId;
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getUserId(): EntityId
    {
        return $this->userId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreationStatus(): UserSetCreationStatus
    {
        return $this->creationStatus;
    }

    public function getStatus(): ?UserSetStatus
    {
        return $this->status;
    }

    public function getStatusDate(): ?\DateTimeImmutable
    {
        return $this->statusDate;
    }

    public function complete(): self
    {
        $new = new self(
            $this->id,
            $this->userId,
            $this->setId,
            $this->createdAt,
            UserSetCreationStatus::COMPLETED,
            $this->status,
            $this->statusDate
        );
        $new->events = [new UserSetCompletedEvent($new)];
        return $new;
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
