<?php

namespace App\Notification\Domain\Model;

/**
 * @author Wilhelm Zwertvaegher
 */
enum NotificationStatus: string
{
    case SENT = 'sent';
    case ERROR = 'error';
    case DISCARDED = 'discarded';
}
