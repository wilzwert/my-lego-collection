<?php

namespace App\Notification\Infrastructure\Sender;

use App\Notification\Domain\Exception\NotificationSendException;
use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationSendResult;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AutoconfigureTag('app.notification_sender')]
interface NotificationSender
{
    public function supports(Notification $notification): bool;

    /**
     * @param Notification $notification
     * @return NotificationSendResult
     */
    public function send(Notification $notification): NotificationSendResult;
}
