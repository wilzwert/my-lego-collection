<?php

namespace App\Notification\Infrastructure\Adapter;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationDispatchResult;
use App\Notification\Domain\Port\Driven\NotificationDispatcher;
use App\Notification\Domain\Port\Driven\NotificationLogRepository;
use App\Notification\Infrastructure\Sender\NotificationSender;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @author Wilhelm Zwertvaegher
 */
class NotificationDispatcherAdapter implements NotificationDispatcher
{
    /**
     * @var array<NotificationSender>
     */
    private array $senders;

    /**
     * @param iterable<NotificationSender> $senders
     */
    public function __construct(
        #[AutowireIterator('app.notification_sender')]
        iterable $senders,
        private readonly NotificationLogRepository $notificationLogRepository
    ) {
        $this->senders = is_array($senders) ? $senders : iterator_to_array($senders);
    }

    /**
     * @param Notification $notification
     * @return array<NotificationDispatchResult>
     */
    public function dispatch(Notification $notification): array
    {
        $results = [];

        foreach ($this->senders as $sender) {
            if ($sender->supports($notification) &&
                // avoid resending a notification which already succeeded
                // this may be useful in case a notification previously partially failed, which could result in a global retry
                !$this->notificationLogRepository->hasSuccess($notification->getMessageId(), $sender->getName())
            ) {
                $senderResult = $sender->send($notification);
                $results[] = new NotificationDispatchResult($sender->getName(), $senderResult->getStatus(), $senderResult->getMessage());
            }
        }

        return $results;
    }
}
