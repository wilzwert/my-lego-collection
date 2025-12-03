<?php

namespace App\Notification\Domain\Ports\Driven;

use App\Notification\Domain\Model\NotificationLog;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationLogRepository
{
    public function save(NotificationLog $notificationLog): void;

}
