<?php

namespace App\Notification\Application\Service;

use App\Notification\Domain\Model\Notification;
use MyLegoCollection\SharedEvent\Command\Command;
use MyLegoCollection\SharedEvent\Message;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationFactory
{
    public function createNotification(Message $message): Notification;

}
