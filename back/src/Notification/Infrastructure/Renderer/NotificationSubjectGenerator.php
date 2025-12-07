<?php

namespace App\Notification\Infrastructure\Renderer;

use App\Notification\Domain\Model\Notification;

/**
 * @author Wilhelm Zwertvaegher
 */
interface NotificationSubjectGenerator
{
    public function generate(Notification $notification): string;
}
