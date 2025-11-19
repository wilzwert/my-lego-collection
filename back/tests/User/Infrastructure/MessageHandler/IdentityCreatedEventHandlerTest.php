<?php

namespace App\Tests\User\Infrastructure\MessageHandler;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventHandler;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Handler\CreateUserHandler;
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
        $handler = $this->createMock(CreateUserHandler::class);
        $handler->expects($this->once())->method('__invoke')->with(
            $this->callback(function (CreateUserCommand $command) {
                self::assertSame('identityId', $command->identityId);
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
        $handler = $this->createMock(CreateUserHandler::class);
        $handler->expects($this->never())->method('__invoke');

        $eventHandler = new IdentityCreatedEventHandler($handler);
        $domainEvent = new DomainEvent('auth.identity.updated', 'identityId');
        $eventHandler($domainEvent);
    }
}
