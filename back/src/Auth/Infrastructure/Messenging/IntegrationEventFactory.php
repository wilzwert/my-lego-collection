<?php

namespace App\Shared\Infrastructure\EventSubscriber;

/**
 * @author Wilhelm Zwertvaegher
 */
namespace App\Auth\Infrastructure\Messenging;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;

class IntegrationEventFactory
{
    public function fromDomainEvent(object $event): object
    {
        return match ($event::class) {

            IdentityCreatedEvent::class =>
            new IdentityCreatedIntegrationEvent(
                $event->type(),
                $event->getIdentity()->getId()->value()
            ),

            default => throw new \LogicException("No IntegrationEvent for ".$event::class)
        };
    }

    public function supports(object $event): bool
    {
        return match ($event::class) {
            IdentityCreatedDomainEvent::class => true,
            default => false
        };
    }
}
