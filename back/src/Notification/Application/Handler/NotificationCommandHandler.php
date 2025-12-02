<?php

namespace App\Notification\Application\Handler;

use App\Notification\Application\Service\NotificationFactory;
use MyLegoCollection\SharedEvent\Command\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class NotificationCommandHandler
{
    public function __construct(
        private NotificationFactory $notificationFactory,
    ) {
    }
    public function __invoke(Command $command): void
    {
        // create notification
        $notification = $this->notificationFactory->createNotification($command);

        // pass to sender

        // save a log
    }
}
