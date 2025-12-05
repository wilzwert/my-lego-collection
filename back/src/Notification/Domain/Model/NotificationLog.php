<?php

namespace App\Notification\Domain\Model;

use App\Notification\Domain\Event\NotificationLogCreatedEvent;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\ProducesDomainEvents;

/**
 * @author Wilhelm Zwertvaegher
 */
final class NotificationLog implements ProducesDomainEvents
{
    /**
     * @var array<DomainEvent>
     */
    private array $events = [];

    public function __construct(
        private readonly EntityId           $id,
        private readonly EntityId           $identityId,
        private readonly ?EntityId          $userId,
        private readonly EntityId          $messageId,
        private readonly NotificationType   $type,
        private string                      $sender,
        private readonly NotificationStatus $status,
        private readonly string             $statusMessage,
        private readonly \DateTimeImmutable $createdAt
    ) {
    }

    public static function create(
        EntityId           $id,
        EntityId           $identityId,
        ?EntityId          $userId,
        EntityId          $messageId,
        NotificationType   $type,
        string             $sender,
        NotificationStatus $status,
        string             $statusMessage,
        \DateTimeImmutable $createdAt,
    ): self {
        $newNotificationLog = new self($id, $identityId, $userId, $messageId, $type, $sender, $status, $statusMessage, $createdAt);
        $newNotificationLog->events = [new NotificationLogCreatedEvent($newNotificationLog)];
        return $newNotificationLog;
    }

    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getIdentityId(): EntityId
    {
        return $this->identityId;
    }

    public function getUserId(): ?EntityId
    {
        return $this->userId;
    }

    public function getMessageId(): EntityId
    {
        return $this->messageId;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function getStatus(): NotificationStatus
    {
        return $this->status;
    }

    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
