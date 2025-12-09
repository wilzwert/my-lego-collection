<?php

namespace App\Tests\Notification\Unit\Infrastructure\EventHandler;

use App\DataFixtures\TestData;
use App\Notification\Application\Handler\NotificationCommandHandler;
use App\Notification\Infrastructure\EventHandler\SendWelcomeNotificationCommandHandler;
use MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class SendWelcomeNotificationCommandHandlerTest extends TestCase
{
    private NotificationCommandHandler&Stub $notificationCommandHandler;

    private SendWelcomeNotificationCommandHandler $underTest;

    protected function setUp(): void
    {
        $this->notificationCommandHandler = $this->createMock(NotificationCommandHandler::class);
        $this->underTest = new SendWelcomeNotificationCommandHandler($this->notificationCommandHandler);
    }

    #[Test]
    public function shouldHandleMessage(): void
    {
        $this->notificationCommandHandler->expects($this->never())->method('__invoke');
        self::assertEquals(SendWelcomeNotificationCommand::class, SendWelcomeNotificationCommandHandler::getMessageHandled());
    }

    #[Test]
    public function shouldHandleSendWelcomeNotificationCommand(): void
    {
        $this->notificationCommandHandler->expects($this->once())->method('__invoke');
        ($this->underTest)(new SendWelcomeNotificationCommand(TestData::IDENTITY1_ID, 'validationToken'));
    }
}
