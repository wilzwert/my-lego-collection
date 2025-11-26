<?php

namespace App\Tests\Auth\Infrastructure\Messaging;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\Auth\Infrastructure\Messenger\AuthIntegrationEventFactory;
use App\Auth\Infrastructure\Messenger\AuthIntegrationEventMiddleware;
use App\Shared\Infrastructure\Messager\IntegrationEventBus;
use MyLegoCollection\SharedEvent\IntegrationEvent;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

#[Group('Messenger')]
class AuthIntegrationEventMiddlewareTest extends TestCase
{
    private AuthIntegrationEventFactory $factory;
    private IntegrationEventBus $integrationBus;
    private LoggerInterface $logger;
    private MiddlewareInterface $nextMiddleware;
    private MiddlewareInterface $middleware;
    private StackInterface $stack;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(AuthIntegrationEventFactory::class);
        $this->integrationBus = $this->createMock(IntegrationEventBus::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // Fake next middleware in stack
        $this->nextMiddleware = $this->createMock(MiddlewareInterface::class);

        $this->stack = $this->createMock(StackInterface::class);
        $this->stack
            ->method('next')
            ->willReturn($this->nextMiddleware);

        $this->middleware = new AuthIntegrationEventMiddleware(
            $this->factory,
            $this->integrationBus,
            $this->logger
        );
    }

    #[Test]
    public function shouldDispatchIntegrationEvent_whenDomainEventIsSupported()
    {
        $message = new class {
        };
        $integrationEvent = $this->createMock(IntegrationEvent::class);

        $envelope = new Envelope($message);

        $this->factory
            ->expects($this->once())
            ->method('supports')
            ->with($message)
            ->willReturn(true);

        $this->factory
            ->expects($this->once())
            ->method('fromDomainEvent')
            ->with($message)
            ->willReturn($integrationEvent);

        $this->logger
            ->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Converting and dispatching message'));

        $this->integrationBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($integrationEvent);

        $this->nextMiddleware
            ->expects($this->once())
            ->method('handle')
            ->with($envelope)
            ->willReturn($envelope);

        $this->middleware->handle($envelope, $this->stack);
    }

    #[Test]
    public function shouldNotDispatch_whenMessageNotSupported()
    {
        $message = new class {
        };
        $envelope = new Envelope($message);

        $this->factory
            ->expects($this->once())
            ->method('supports')
            ->with($message)
            ->willReturn(false);

        $this->factory
            ->expects($this->never())
            ->method('fromDomainEvent');

        $this->logger
            ->expects($this->never())
            ->method('info');

        $this->integrationBus
            ->expects($this->never())
            ->method('dispatch');

        $this->nextMiddleware
            ->expects($this->once())
            ->method('handle')
            ->with($envelope)
            ->willReturn($envelope);

        $this->middleware->handle($envelope, $this->stack);
    }
}
