<?php

namespace App\Notification\Infrastructure\Persistence\Doctrine\Entity;

use App\Auth\Domain\Model\Identity;
use App\Auth\Infrastructure\Persistence\Doctrine\Entity\DoctrineIdentity;
use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Model\NotificationType;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wilhelm Zwertvaegher
 */
#[ORM\Entity]
#[ORM\Table(name: "notifications")]
class DoctrineNotificationLog
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36)]
    private string $identityId;

    #[ORM\Column(type: "string", length: 36, nullable: true)]
    private ?string $userId;

    #[ORM\Column(type: "string", enumType: NotificationType::class)]
    private NotificationType $type;

    #[ORM\Column(type: "string")]
    private string $sender;

    #[ORM\Column(type: "string", enumType: NotificationStatus::class)]
    private NotificationStatus $status;

    #[ORM\Column(type: "string")]
    private string $statusMessage;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdentityId(): string
    {
        return $this->identityId;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
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

    public function fromDomain(NotificationLog $notificationLog): DoctrineNotificationLog
    {
        $this->id = $notificationLog->getId();
        $this->identityId = $notificationLog->getIdentityId();
        $this->userId = $notificationLog->getUserId();
        $this->type = $notificationLog->getType();
        $this->sender = $notificationLog->getSender();
        $this->status = $notificationLog->getStatus();
        $this->statusMessage = $notificationLog->getStatusMessage();
        $this->createdAt = $notificationLog->getCreatedAt();
        return $this;
    }

    public function toDomain(): NotificationLog
    {
        return new NotificationLog(
            id: EntityId::fromString($this->id),
            identityId: EntityId::fromString($this->identityId),
            userId: EntityId::fromString($this->userId),
            type: $this->type,
            sender: $this->sender,
            status: $this->status,
            statusMessage: $this->statusMessage,
            createdAt: $this->createdAt
        );
    }
}
