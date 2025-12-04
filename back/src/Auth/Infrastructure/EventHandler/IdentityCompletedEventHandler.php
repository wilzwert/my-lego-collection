<?php

namespace App\Auth\Infrastructure\EventHandler;

use App\Auth\Application\Orchestrator\IdentityCompletedOrchestrator;
use App\Auth\Application\Orchestrator\IdentityCreatedOrchestrator;
use App\Auth\Domain\Event\IdentityCompletedEvent;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Infrastructure\EventHandler\DomainEventHandler;
use App\Shared\Infrastructure\EventHandler\IntegrationEventHandler;
use App\Shared\Infrastructure\EventHandler\MessageHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements DomainEventHandler<IdentityCreatedEvent>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler(fromTransport: 'sync', priority: 10)]
readonly class IdentityCompletedEventHandler implements DomainEventHandler
{
    public function __construct(private IdentityCompletedOrchestrator $orchestrator)
    {
    }

    public static function getMessageHandled(): string
    {
        return IdentityCompletedEvent::class;
    }

    public function __invoke(IdentityCompletedEvent $event): void
    {
        ($this->orchestrator)($event);
    }
}
