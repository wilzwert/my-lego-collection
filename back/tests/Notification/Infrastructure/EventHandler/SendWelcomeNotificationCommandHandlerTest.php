<?php

namespace App\Tests\Notification\Infrastructure\EventHandler;

use App\Notification\Infrastructure\EventHandler\SendWelcomeNotificationCommandHandler;
use MyLegoCollection\SharedEvent\Command\SendWelcomeNotificationCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class SendWelcomeNotificationCommandHandlerTest extends TestCase
{
    #[Test]
    public function shouldHandleMessage(): void
    {
        self::assertEquals(SendWelcomeNotificationCommand::class, SendWelcomeNotificationCommandHandler::getMessageHandled());
    }

    #[Test]
    public function shouldHandleSendWelcomeNotificationCommand(): void
    {
        self::expectNotToPerformAssertions();
        $handler = new SendWelcomeNotificationCommandHandler();
        $handler(new SendWelcomeNotificationCommand('test@example.com', 'user', 'validationToken'));
    }
}

