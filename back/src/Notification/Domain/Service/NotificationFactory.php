<?php

namespace App\Notification\Domain\Service;

use App\Notification\Domain\Model\Notification;
use MyLegoCollection\SharedContracts\Message;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationFactory
{
    public function createNotification(Message $message): Notification;

}
