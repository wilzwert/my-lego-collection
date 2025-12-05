<?php

namespace App\Notification\Domain\Port\Driven;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationDispatchResult;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationDispatcher
{
    /**
     * @param Notification $notification
     * @return array<NotificationDispatchResult>
     */
    public function dispatch(Notification $notification): array;
}
