<?php

namespace App\Notification\Infrastructure\Sender;

use App\Notification\Domain\Model\NotificationStatus;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class NotificationSenderResult
{
    public function __construct(
        private NotificationStatus $status,
        private string $message,
    ) {
    }

    public function getStatus(): NotificationStatus
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
