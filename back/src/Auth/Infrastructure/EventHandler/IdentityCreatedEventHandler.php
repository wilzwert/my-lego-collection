<?php

namespace App\Auth\Infrastructure\EventHandler;

use App\Auth\Application\Orchestrator\IdentityCreatedOrchestrator;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Infrastructure\EventHandler\DomainEventHandler;
use App\Shared\Infrastructure\EventHandler\IntegrationEventHandler;
use App\Shared\Infrastructure\EventHandler\MessageHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements DomainEventHandler<IdentityCreatedEvent>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class IdentityCreatedEventHandler implements DomainEventHandler
{
    public function __construct(private IdentityCreatedOrchestrator $orchestrator)
    {
    }

    /**
     * @return class-string<IdentityCreatedEvent>
     */
    public static function getMessageHandled(): string
    {
        return IdentityCreatedEvent::class;
    }

    public function __invoke(IdentityCreatedEvent $event): void
    {
        ($this->orchestrator)($event);
    }
}
