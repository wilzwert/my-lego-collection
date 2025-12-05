<?php

namespace App\Notification\Application\Handler;

use App\Notification\Domain\Port\Driven\NotificationDispatcher;
use App\Notification\Domain\Service\NotificationFactory;
use App\Notification\Domain\Port\Driven\NotificationLogRepository;
use App\Notification\Domain\Service\NotificationLogService;
use App\Shared\Domain\Port\Driven\TransactionProvider;
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
        private NotificationLogRepository $notificationLogRepository,
        private TransactionProvider       $transactionProvider
    ) {
    }
    public function __invoke(Command $command): void
    {
        // create notification to be sent
        $notification = $this->notificationFactory->createNotification($command);

        // pass to dispatcher
        $results = $this->notificationDispatcher->dispatch($notification);

        $this->transactionProvider->transactional(function () use ($notification, $results) {
            // save logs for the notification
            foreach ($results as $result) {
                $this->notificationLogRepository->save(
                    $this->notificationLogService->createFromNotification(
                        $notification,
                        $result
                    )
                );
            }
        });
    }
}
