<?php

namespace App\Tests\User\Infrastructure\MessageHandler;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Model\EntityId;
use App\User\Application\Handler\IdentityCreatedHandler;
use App\User\Infrastructure\EventHandler\IdentityCreatedEventHandler;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
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
            $this->callback(function (IdentityCreatedIntegrationEvent $event) {
                self::assertSame('identityId', $event->getId());
                return true;
            })
        );
        $eventHandler = new IdentityCreatedEventHandler($handler);
        $integrationEvent = new IdentityCreatedIntegrationEvent('identityId');
        $eventHandler($integrationEvent);
    }

}
