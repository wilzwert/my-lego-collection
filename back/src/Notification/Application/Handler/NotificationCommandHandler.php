<?php

namespace App\Notification\Application\Handler;

use App\Notification\Domain\Exception\NotificationSendException;
use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Ports\Driven\NotificationLogRepository;
use App\Notification\Domain\Ports\Driven\NotificationDispatcher;
use App\Notification\Domain\Service\NotificationFactory;
use App\Notification\Domain\Service\NotificationLogService;
use MyLegoCollection\SharedEvent\Command\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class NotificationCommandHandler
{
    public function __construct(
        private NotificationFactory       $notificationFactory,
        private NotificationDispatcher    $notificationDispatcher,
        private NotificationLogService    $notificationLogService,
        private NotificationLogRepository $notificationLogRepository
    ) {
    }
    public function __invoke(Command $command): void
    {
        // create notification to be sent
        $notification = $this->notificationFactory->createNotification($command);

        // pass to dispatcher
        $results = $this->notificationDispatcher->dispatch($notification);

        // save logs for the notification
        foreach ($results as $result) {
            $this->notificationLogRepository->save(
                $this->notificationLogService->createFromNotification(
                    $notification,
                    $result
                )
            );
        }
    }
}
