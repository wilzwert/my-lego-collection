<?php

namespace App\Notification\Infrastructure\Renderer;

use App\Notification\Domain\Model\Notification;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AutoconfigureTag('app.file_storage_provider')]
interface NotificationRenderer
{
    public function render(Notification $notification): string;
}
