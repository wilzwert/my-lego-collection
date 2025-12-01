<?php

namespace App\Auth\Infrastructure\EventHandler;

use App\Auth\Application\Handler\UserCreatedHandler;
use App\Auth\Application\Orchestrator\IdentityCreatedOrchestrator;
use App\Shared\Infrastructure\EventHandler\IntegrationEventHandler;
use App\Shared\Infrastructure\EventHandler\MessageHandler;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements IntegrationEventHandler<UserCreatedIntegrationEvent>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class UserCreatedIntegrationEventHandler implements IntegrationEventHandler
{
    public function __construct(private UserCreatedHandler $userCreatedHandler)
    {
    }

    public static function getMessageHandled(): string
    {
        return UserCreatedIntegrationEvent::class;
    }

    public function __invoke(UserCreatedIntegrationEvent $event): void
    {
        ($this->userCreatedHandler)($event);
    }
}
