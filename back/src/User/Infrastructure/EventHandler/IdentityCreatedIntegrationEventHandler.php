<?php

namespace App\User\Infrastructure\EventHandler;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Infrastructure\EventHandler\IntegrationEventHandler;
use App\User\Application\Handler\IdentityCreatedHandler;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler(fromTransport: 'sync', priority: 10)]
readonly class IdentityCreatedIntegrationEventHandler implements IntegrationEventHandler
{
    public function __construct(private readonly IdentityCreatedHandler $createUserHandler)
    {
    }


    public static function getEventHandled(): string
    {
        return IdentityCreatedIntegrationEvent::class;
    }

    public function __invoke(IdentityCreatedIntegrationEvent $event): void
    {
        ($this->createUserHandler)($event);
    }
}
