<?php

namespace App\Notification\Infrastructure\Sender;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Model\NotificationType;
use App\Notification\Infrastructure\Renderer\NotificationRenderer;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
final class SmsSender implements NotificationSender
{

    private const string NAME = 'sms';

    public function __construct(
        private readonly NotificationRenderer $textRenderer,
        private readonly TexterInterface      $texter
    ) {
    }

    public function supports(Notification $notification): bool
    {
        return in_array($notification->getType(), [NotificationType::WELCOME]);
    }

    public function send(Notification $notification): NotificationSenderResult
    {
        $content = $this->textRenderer->render($notification, $this);

        $smsNotification = new SmsMessage(
            '+336123456789',
            $content,
            '+336123456789'
        );

        try {
            $this->texter->send($smsNotification);
            return new NotificationSenderResult(NotificationStatus::SENT, 'Sms sent : ' . $content);
        } catch (TransportExceptionInterface $e) {
            return new NotificationSenderResult(NotificationStatus::ERROR, 'Sms could not be send : ' . $e->getMessage());
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
