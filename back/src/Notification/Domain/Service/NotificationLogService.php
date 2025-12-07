<?php

namespace App\Notification\Domain\Service;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationDispatchResult;
use App\Notification\Domain\Model\NotificationStatus;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationLogService
{
    public function createFromNotification(Notification $notification, NotificationDispatchResult $result): NotificationLog;
}
