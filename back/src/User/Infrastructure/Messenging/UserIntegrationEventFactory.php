<?php

namespace App\User\Infrastructure\Messenging;

/**
 * @author Wilhelm Zwertvaegher
 */


use App\User\Domain\Event\UserCreatedEvent;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
use MyLegoCollection\SharedEvent\IntegrationEvent;
use MyLegoCollection\SharedEvent\UserCreatedIntegrationEvent;

class UserIntegrationEventFactory
{
    public function fromDomainEvent(object $event): IntegrationEvent
    {
        return match ($event::class) {

            UserCreatedEvent::class =>
            new UserCreatedIntegrationEvent(
                $event->getUser()->getId()->value()
            ),

            default => throw new \LogicException("No IntegrationEvent for " . $event::class)
        };
    }

    public function supports(object $event): bool
    {
        return match ($event::class) {
            UserCreatedEvent::class => true,
            default => false
        };
    }
}
