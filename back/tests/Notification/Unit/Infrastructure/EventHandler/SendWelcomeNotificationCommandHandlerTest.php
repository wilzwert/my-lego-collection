<?php

namespace App\Tests\Notification\Unit\Infrastructure\EventHandler;

use App\Notification\Application\Handler\NotificationCommandHandler;
use App\Notification\Infrastructure\EventHandler\SendWelcomeNotificationCommandHandler;
use App\Tests\Utilities\TestData;
use MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class SendWelcomeNotificationCommandHandlerTest extends TestCase
{
    private NotificationCommandHandler&MockObject $notificationCommandHandler;

    private SendWelcomeNotificationCommandHandler $underTest;

    protected function setUp(): void
    {
        $this->notificationCommandHandler = $this->createMock(NotificationCommandHandler::class);
        $this->underTest = new SendWelcomeNotificationCommandHandler($this->notificationCommandHandler);
    }

    #[Test]
    public function shouldHandleMessage(): void
    {
        self::assertEquals(SendWelcomeNotificationCommand::class, SendWelcomeNotificationCommandHandler::getMessageHandled());
    }

    #[Test]
    public function shouldHandleSendWelcomeNotificationCommand(): void
    {
        self::expectNotToPerformAssertions();
        ($this->underTest)(new SendWelcomeNotificationCommand(TestData::EXISTING_IDENTITY_ID, 'validationToken'));
    }
}
