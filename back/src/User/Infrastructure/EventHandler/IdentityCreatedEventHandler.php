<?php

namespace App\User\Infrastructure\EventHandler;

use App\Shared\Infrastructure\EventHandler\IntegrationEventHandler;
use App\User\Application\Handler\IdentityCreatedHandler;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler(fromTransport: 'sync', priority: 10)]
readonly class IdentityCreatedEventHandler implements IntegrationEventHandler
{
    public function __construct(private readonly IdentityCreatedHandler $createUserHandler)
    {
    }


    public static function getEventHandled(): string
    {
        return 'auth.identity.created';
    }

    public function __invoke(IdentityCreatedIntegrationEvent $event): void
    {
        ($this->createUserHandler)($event);
    }
}
