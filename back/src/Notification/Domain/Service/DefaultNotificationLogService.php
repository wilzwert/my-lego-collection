<?php

namespace App\Notification\Domain\Service;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationDispatchResult;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Port\Driven\Clock;

/**
 * @author Wilhelm Zwertvaegher
 */
class DefaultNotificationLogService implements NotificationLogService
{
    public function __construct(private readonly Clock $clock)
    {
    }

    public function createFromNotification(Notification $notification, NotificationDispatchResult $result): NotificationLog
    {
        return NotificationLog::create(
            EntityId::generate(),
            EntityId::fromString($notification->getIdentityInfo()->getIdentityId()),
            null,
            EntityId::fromString($notification->getMessageId()),
            $notification->getType(),
            $result->getSender(),
            $result->getStatus(),
            $result->getMessage(),
            $this->clock->getNow()
        );
    }
}
