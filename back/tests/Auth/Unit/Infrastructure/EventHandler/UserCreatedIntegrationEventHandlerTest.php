<?php

namespace App\Tests\Auth\Unit\Infrastructure\EventHandler;

use App\Auth\Application\Handler\UserCreatedHandler;
use App\Auth\Infrastructure\EventHandler\UserCreatedIntegrationEventHandler;
use App\Tests\Utilities\TestData;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserCreatedIntegrationEventHandlerTest extends TestCase
{

    private UserCreatedHandler&MockObject $userCreatedHandler;

    private UserCreatedIntegrationEventHandler $userCreatedIntegrationEventHandler;

    protected function setUp(): void
    {
        $this->userCreatedHandler = $this->createMock(UserCreatedHandler::class);
        $this->userCreatedIntegrationEventHandler = new UserCreatedIntegrationEventHandler($this->userCreatedHandler);
    }

    #[Test]
    public function shouldHandleMessage(): void
    {
        self::assertEquals(UserCreatedIntegrationEvent::class, UserCreatedIntegrationEventHandler::getMessageHandled());
    }

    #[Test]
    public function shouldHandleSendWelcomeNotificationCommand(): void
    {
        $event = new UserCreatedIntegrationEvent(TestData::EXISTING_USER_ID, TestData::EXISTING_IDENTITY_ID);
        $this->userCreatedHandler->expects(self::once())->method('__invoke')->with($event);
        ($this->userCreatedIntegrationEventHandler)($event);
    }
}
