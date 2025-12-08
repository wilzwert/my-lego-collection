<?php

namespace App\CollectionManagement\Infrastructure\EventHandler;

use App\CollectionManagement\Application\Orchestrator\SetCompletedOrchestrator;
use App\CollectionManagement\Domain\Event\SetCompletedEvent;
use App\Shared\Infrastructure\EventHandler\DomainEventHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements DomainEventHandler<SetCompletedEvent>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class SetCompletedEventHandler implements DomainEventHandler
{
    public function __construct(private SetCompletedOrchestrator $setCompletedOrchestrator)
    {
    }

    public static function getMessageHandled(): string
    {
        return SetCompletedEvent::class;
    }

    public function __invoke(SetCompletedEvent $setCompletedEvent): void
    {
        ($this->setCompletedOrchestrator)($setCompletedEvent);
    }
}
