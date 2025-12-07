<?php

namespace App\Notification\Infrastructure\Renderer;

use App\Notification\Domain\Exception\UnknownNotificationTypeException;
use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\NotificationType;

/**
 * @author Wilhelm Zwertvaegher
 */
class DefaultNotificationSubjectGenerator implements NotificationSubjectGenerator
{

    /**
     * @throws UnknownNotificationTypeException
     */
    public function generate(Notification $notification): string
    {
        return match ($notification->getType()) {
            NotificationType::WELCOME => 'Welcome ' . $notification->getIdentityInfo()->getUsername(),
            default => throw new UnknownNotificationTypeException()
        };
    }
}
