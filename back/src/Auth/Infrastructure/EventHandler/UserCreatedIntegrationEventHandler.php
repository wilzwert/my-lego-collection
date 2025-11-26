<?php

namespace App\Auth\Infrastructure\EventHandler;

use App\Auth\Application\Orchestrator\IdentityCreatedOrchestrator;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Infrastructure\EventHandler\DomainEventHandler;
use App\Shared\Infrastructure\EventHandler\IntegrationEventHandler;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class UserCreatedIntegrationEventHandler implements IntegrationEventHandler
{
    public function __construct(private IdentityCreatedOrchestrator $orchestrator)
    {
    }

    public static function getEventHandled(): string
    {
        return UserCreatedIntegrationEvent::class;
    }

    public function __invoke(UserCreatedIntegrationEvent $event): void
    {
        ($this->orchestrator)($event);
    }
}
