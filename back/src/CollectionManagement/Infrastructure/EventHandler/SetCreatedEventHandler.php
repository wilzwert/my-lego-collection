<?php

namespace App\CollectionManagement\Infrastructure\EventHandler;

use App\CollectionManagement\Application\Orchestrator\SetCreatedOrchestrator;
use App\CollectionManagement\Domain\Event\SetCreatedEvent;
use App\Shared\Infrastructure\EventHandler\DomainEventHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements DomainEventHandler<SetCreatedEvent>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class SetCreatedEventHandler implements DomainEventHandler
{
    public function __construct(private SetCreatedOrchestrator $setCreatedOrchestrator)
    {
    }

    public static function getMessageHandled(): string
    {
        return SetCreatedEvent::class;
    }

    public function __invoke(SetCreatedEvent $setCreatedEvent): void
    {
        ($this->setCreatedOrchestrator)($setCreatedEvent);
    }
}
