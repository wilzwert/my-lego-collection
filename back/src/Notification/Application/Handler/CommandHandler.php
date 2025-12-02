<?php

namespace App\Notification\Application\Handler;

use App\Notification\Application\Service\DefaultNotificationFactory;
use MyLegoCollection\SharedEvent\Command\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class CommandHandler
{
    public function __construct(
        private DefaultNotificationFactory $notificationFactory,
    ) {
    }
    public function __invoke(Command $command): void
    {
        $notification = $this->notificationFactory->createNotification($command);
    }
}
