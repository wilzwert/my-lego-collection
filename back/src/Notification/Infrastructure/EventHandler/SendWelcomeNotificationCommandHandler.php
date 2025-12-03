<?php

namespace App\Notification\Infrastructure\EventHandler;

use App\Notification\Application\Handler\NotificationCommandHandler;
use App\Shared\Infrastructure\EventHandler\CommandHandler;
use App\Shared\Infrastructure\EventHandler\MessageHandler;
use MyLegoCollection\SharedEvent\Command\SendWelcomeNotificationCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements CommandHandler<SendWelcomeNotificationCommand>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler(fromTransport: "async")]
readonly class SendWelcomeNotificationCommandHandler implements CommandHandler
{
    public function __construct(
        private NotificationCommandHandler $notificationCommandHandler
    ) {
    }

    public static function getMessageHandled(): string
    {
        return SendWelcomeNotificationCommand::class;
    }

    public function __invoke(SendWelcomeNotificationCommand $command): void
    {
        ($this->notificationCommandHandler)($command);
    }
}
