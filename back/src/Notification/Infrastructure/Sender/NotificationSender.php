<?php

namespace App\Notification\Infrastructure\Sender;

use App\Notification\Domain\Exception\NotificationSendException;
use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationDispatchResult;
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
     * @return NotificationDispatchResult
     */
    public function send(Notification $notification): NotificationSenderResult;

    public function getName(): string;
}
