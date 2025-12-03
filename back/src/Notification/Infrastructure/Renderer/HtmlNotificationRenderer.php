<?php

namespace App\Notification\Infrastructure\Renderer;

use App\Notification\Domain\Model\Notification;
use Twig\Environment;

/**
 * @author Wilhelm Zwertvaegher
 */
class HtmlNotificationRenderer implements NotificationRenderer
{

    public function __construct(
        private Environment $twig,
    ) {
    }

    public function render(Notification $notification): string
    {
        return $this->twig->render('notifications/'.$notification->getType()->value.'.html.twig', $notification->getPayload());
    }
}
