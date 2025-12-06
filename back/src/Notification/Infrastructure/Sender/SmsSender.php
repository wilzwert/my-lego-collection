<?php

namespace App\Notification\Infrastructure\Sender;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Model\NotificationType;
use App\Notification\Infrastructure\Renderer\NotificationRenderer;

/**
 * @author Wilhelm Zwertvaegher
 */
final class SmsSender implements NotificationSender
{

    private const string NAME = 'sms';

    private int $smsSentCount = 0;

    public function __construct(
        private readonly NotificationRenderer $textRenderer
    ) {
    }

    public function supports(Notification $notification): bool
    {
        return in_array($notification->getType(), [NotificationType::WELCOME]);
    }

    public function send(Notification $notification): NotificationSenderResult
    {
        $content = $this->textRenderer->render($notification, $this);

        $this->smsSentCount++;

        return new NotificationSenderResult(NotificationStatus::SENT, 'Sms sent : '.$content);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSmsSentCount(): int
    {
        return $this->smsSentCount;
    }
}
