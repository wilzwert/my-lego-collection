<?php

namespace App\Notification\Domain\Port\Driven;

use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationLogRepository
{
    /**
     * @param string $messageId
     * @return array<NotificationLog>
     */
    public function findByMessageId(string $messageId): array;

    /**
     * @param string $messageId
     * @param string $sender
     * @return array<NotificationLog>
     */
    public function findByMessageIdAndSender(string $messageId, string $sender): array;

    /**
     * @param string $messageId
     * @param string $sender
     * @param NotificationStatus $status
     * @return array<NotificationLog>
     */
    public function findByMessageIdAndSenderAndStatus(string $messageId, string $sender, NotificationStatus $status): array;

    /**
     * @param string $messageId
     * @param NotificationStatus $status
     * @return array<NotificationLog>
     */
    public function findByMessageIdAndStatus(string $messageId, NotificationStatus $status): array;

    public function hasSuccess(string $messageId, string $sender): bool;

    public function save(NotificationLog $notificationLog): void;

}
