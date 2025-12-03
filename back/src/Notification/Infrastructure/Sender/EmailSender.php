<?php

namespace App\Notification\Infrastructure\Sender;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationSendResult;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Infrastructure\Renderer\NotificationRenderer;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class EmailSender implements NotificationSender
{

    public function __construct(private NotificationRenderer $renderer)
    {
    }

    public function supports(Notification $notification): bool
    {
        return true;
    }

    public function send(Notification $notification): NotificationSendResult
    {
        $content = $this->renderer->render($notification);

        // TODO : actually send the email
        $email = $notification->getIdentityInfo()->getEmail();

        return new NotificationSendResult('email', NotificationStatus::SENT, 'Email sent');
    }
}
