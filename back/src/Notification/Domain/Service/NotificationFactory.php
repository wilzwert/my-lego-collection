<?php

namespace App\Notification\Domain\Service;

use App\Notification\Domain\Model\Notification;
use MyLegoCollection\SharedEvent\Message;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationFactory
{
    public function createNotification(Message $message): Notification;

}
