<?php

namespace App\Notification\Infrastructure\Persistence\Doctrine\Entity;

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

    #[ORM\Column(type: "string", length: 36)]
    private string $messageId;

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


    public function fromDomain(NotificationLog $notificationLog): DoctrineNotificationLog
    {
        $this->id = $notificationLog->getId();
        $this->identityId = $notificationLog->getIdentityId();
        $this->userId = $notificationLog->getUserId();
        $this->messageId = $notificationLog->getMessageId();
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
            userId: $this->userId ? EntityId::fromString($this->userId) : null,
            messageId: EntityId::fromString($this->messageId),
            type: $this->type,
            sender: $this->sender,
            status: $this->status,
            statusMessage: $this->statusMessage,
            createdAt: $this->createdAt
        );
    }
}
