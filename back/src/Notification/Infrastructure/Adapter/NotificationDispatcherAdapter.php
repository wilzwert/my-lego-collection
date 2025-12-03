<?php

namespace App\Notification\Infrastructure\Adapter;

use App\Notification\Domain\Exception\NotificationSendException;
use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationSendResult;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Ports\Driven\NotificationDispatcher;
use App\Notification\Infrastructure\Sender\NotificationSender;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
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
        iterable $senders
    ) {
        $this->senders = is_array($senders) ? $senders : iterator_to_array($senders);
    }

    /**
     * @param Notification $notification
     * @return array<NotificationSendResult>
     */
    public function dispatch(Notification $notification): array
    {
        $results = [];

        foreach ($this->senders as $sender) {
            if ($sender->supports($notification)) {
                $results[] = $sender->send($notification);
            }
        }

        return $results;
    }
}
