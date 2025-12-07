<?php

namespace App\Notification\Domain\Model;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class NotificationDispatchResult
{
    public function __construct(
        private string $sender,
        private NotificationStatus $status,
        private string $message,
    ) {
    }

    public function getSender(): string
    {
        return $this->sender;
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
