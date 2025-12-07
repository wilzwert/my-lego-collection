<?php

namespace App\Notification\Infrastructure\Renderer;

use App\Notification\Domain\Model\Notification;
use App\Notification\Infrastructure\Sender\NotificationSender;
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

    public function render(Notification $notification, NotificationSender $sender): string
    {
        return $this->twig->render(
            'notifications/'
            . $sender->getName() . '/'
            . $notification->getType()->value . '.txt.twig',
            $notification->getPayload()
        );
    }
}
