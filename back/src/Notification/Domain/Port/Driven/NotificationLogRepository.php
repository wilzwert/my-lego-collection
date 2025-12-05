<?php

namespace App\Notification\Domain\Port\Driven;

use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationLogRepository
{
    public function findByMessageId(string $messageId): array;

    public function findByMessageIdAndSender(string $messageId, string $sender): array;

    public function findByMessageIdAndSenderAndStatus(string $messageId, string $sender, NotificationStatus $status): array;

    public function findByMessageIdAndStatus(string $messageId, NotificationStatus $status): array;

    public function hasSuccess(string $messageId, string $sender): bool;

    public function save(NotificationLog $notificationLog): void;

}
