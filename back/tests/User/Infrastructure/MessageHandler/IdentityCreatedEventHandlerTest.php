<?php

namespace App\Tests\User\Infrastructure\MessageHandler;

use App\Shared\Domain\Event\DomainEvent;
use App\User\Application\Handler\IdentityCreatedHandler;
use App\User\Infrastructure\EventHandler\IdentityCreatedEventHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedEventHandlerTest extends TestCase
{
    #[Test]
    public function shouldHandleAuthIdentityCreatedEvent(): void
    {
        self::assertSame('auth.identity.created', IdentityCreatedEventHandler::getEventHandled());
    }

    #[Test]
    public function shouldCreateUser(): void
    {
        $handler = $this->createMock(IdentityCreatedHandler::class);
        $handler->expects($this->once())->method('__invoke')->with(
            $this->callback(function (DomainEvent $event) {
                self::assertSame('identityId', $event->id());
                return true;
            })
        );
        $eventHandler = new IdentityCreatedEventHandler($handler);
        $domainEvent = new DomainEvent('auth.identity.created', 'identityId');
        $eventHandler($domainEvent);
    }

    #[Test]
    public function shouldDoNothing_whenUnhandledEvent(): void
    {
        $handler = $this->createMock(IdentityCreatedHandler::class);
        $handler->expects($this->never())->method('__invoke');

        $eventHandler = new IdentityCreatedEventHandler($handler);
        $domainEvent = new DomainEvent('auth.identity.updated', 'identityId');
        $eventHandler($domainEvent);
    }
}
