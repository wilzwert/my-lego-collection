<?php

namespace App\Notification\Domain\Event;

use App\Auth\Domain\Model\Identity;
use App\Notification\Domain\Model\NotificationLog;
use App\Shared\Domain\Event\DomainEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
class NotificationLogCreatedEvent extends DomainEvent
{
    private const string TYPE = 'notification.log.created';

    private readonly NotificationLog $notificationLog;

    public function __construct(NotificationLog $notificationLog, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
        $this->notificationLog = $notificationLog;
    }

    public function getNotificationLog(): NotificationLog
    {
        return $this->notificationLog;
    }
}
