<?php

namespace App\User\Infrastructure\EventHandler;

use App\Shared\Infrastructure\EventHandler\DomainEventHandler;
use App\User\Application\Orchestrator\UserCreatedOrchestrator;
use App\User\Domain\Event\UserCreatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler(fromTransport: 'sync', priority: 10)]
readonly class UserCreatedEventHandler implements DomainEventHandler
{
    public function __construct(private UserCreatedOrchestrator $userCreatedOrchestrator)
    {
    }


    public static function getEventHandled(): string
    {
        return UserCreatedEvent::class;
    }

    public function __invoke(UserCreatedEvent $event): void
    {
        ($this->userCreatedOrchestrator)($event);
    }
}
