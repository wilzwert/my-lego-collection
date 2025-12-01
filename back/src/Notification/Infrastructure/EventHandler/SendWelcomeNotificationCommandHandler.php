<?php

namespace App\Notification\Infrastructure\EventHandler;

use App\Shared\Infrastructure\EventHandler\CommandHandler;
use App\Shared\Infrastructure\EventHandler\MessageHandler;
use MyLegoCollection\SharedEvent\Command\SendWelcomeNotificationCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements CommandHandler<SendWelcomeNotificationCommand>
 * @author Wilhelm Zwertvaegher
 */

#[AsMessageHandler]
class SendWelcomeNotificationCommandHandler implements CommandHandler
{
    public static function getMessageHandled(): string
    {
        return SendWelcomeNotificationCommand::class;
    }

    public function __invoke(SendWelcomeNotificationCommand $command): void
    {

    }
}
