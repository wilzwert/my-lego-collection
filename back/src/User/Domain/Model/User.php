<?php

namespace App\User\Domain\Model;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Event\AvatarUpdatedEvent;
use App\User\Domain\Event\UserCreatedEvent;

class User
{
    /**
     * @var array<DomainEvent>
     */
    private array $events;


    /**
     * @param EntityId $id
     * @param EntityId $identityId
     */
    public function __construct(
        private EntityId           $id,
        private EntityId           $identityId,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
        private ?StoredFile $avatar = null
    ) {
    }

    public static function create(
        EntityId           $id,
        EntityId           $identityId,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        ?StoredFile $avatar = null
    ): self {
        $newUser = new self($id, $identityId, $createdAt, $updatedAt, $avatar);
        $newUser->events[] = new UserCreatedEvent($newUser);
        return $newUser;
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getIdentityId(): EntityId
    {
        return $this->identityId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getAvatar(): ?StoredFile
    {
        return $this->avatar;
    }

    public function setAvatar(?StoredFile $avatar): self
    {
        $result = new User($this->id, $this->identityId, $this->createdAt, new \DateTimeImmutable(), $avatar);
        $result->events = [new AvatarUpdatedEvent($result)];
        return $result;
    }

    /**
     * @return array<DomainEvent>
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
