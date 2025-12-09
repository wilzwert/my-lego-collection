<?php

namespace App\CollectionManagement\Infrastructure\EventHandler;

use App\CollectionManagement\Application\Orchestrator\SetCreatedOrchestrator;
use App\CollectionManagement\Application\Orchestrator\UserSetCompletedOrchestrator;
use App\CollectionManagement\Application\Orchestrator\UserSetCreatedOrchestrator;
use App\CollectionManagement\Domain\Event\SetCreatedEvent;
use App\CollectionManagement\Domain\Event\UserSetCompletedEvent;
use App\CollectionManagement\Domain\Event\UserSetCreatedEvent;
use App\Shared\Infrastructure\EventHandler\DomainEventHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements DomainEventHandler<UserSetCreatedEvent>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class UserSetCompletedEventHandler implements DomainEventHandler
{
    public function __construct(private UserSetCompletedOrchestrator $userSetCompletedOrchestrator)
    {
    }

    public static function getMessageHandled(): string
    {
        return UserSetCompletedEvent::class;
    }

    public function __invoke(UserSetCompletedEvent $userSetCreatedEvent): void
    {
        ($this->userSetCompletedOrchestrator)($userSetCreatedEvent);
    }
}
