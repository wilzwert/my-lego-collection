<?php

namespace App\Notification\Domain\Ports\Driven;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationSendResult;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationDispatcher
{
    /**
     * @param Notification $notification
     * @return array<NotificationSendResult>
     */
    public function dispatch(Notification $notification): array;
}
