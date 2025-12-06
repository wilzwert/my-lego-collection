<?php

namespace App\Tests\Notification\Integration\Infrastructure\Sender;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Infrastructure\Sender\NotificationSender;
use App\Notification\Infrastructure\Sender\NotificationSenderResult;

/**
 * @author Wilhelm Zwertvaegher
 */
class ErrorSender implements NotificationSender
{

    public function supports(Notification $notification): bool
    {
        return true;
    }

    public function send(Notification $notification): NotificationSenderResult
    {
        return new NotificationSenderResult(NotificationStatus::ERROR, 'test_error');
    }

    public function getName(): string
    {
        return 'test_error_sender';
    }
}
