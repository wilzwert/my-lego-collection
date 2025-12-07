<?php

namespace App\Notification\Infrastructure\Sender;

use App\Notification\Domain\Model\Notification;
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
     * @return NotificationSenderResult
     */
    public function send(Notification $notification): NotificationSenderResult;

    public function getName(): string;
}
