<?php

namespace App\User\Infrastructure\EventHandler;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
class IdentityCreatedEventHandler implements DomainEventHandler
{

    public static function getEventHandled(): string
    {
        return 'auth.identity.created';
    }

    public function handle(DomainEvent $event): void
    {
        ($this)($event);
    }

    public function __invoke(DomainEvent $event): void
    {
        if ($event->type() != self::getEventHandled()) {
            return;
        }

        // TODO : create empty user

    }
}
