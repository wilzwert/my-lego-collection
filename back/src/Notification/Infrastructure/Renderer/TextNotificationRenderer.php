<?php

namespace App\Notification\Infrastructure\Renderer;

use App\Notification\Domain\Model\Notification;
use Twig\Environment;

/**
 * @author Wilhelm Zwertvaegher
 */
class TextNotificationRenderer implements NotificationRenderer
{

    public function __construct(
        private Environment $twig,
    )
    {
    }

    public function render(Notification $notification): string
    {
        return $this->twig->render('notifications/' . $notification->getType()->value . '.txt.twig', $notification->getPayload());
    }
}
