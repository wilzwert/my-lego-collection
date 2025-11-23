<?php

namespace App\Auth\Infrastructure\Messenging;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\Auth\Domain\Event\IdentityCreatedEvent;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
use MyLegoCollection\SharedEvent\IntegrationEvent;

class AuthIntegrationEventFactory
{
    public function fromDomainEvent(object $event): IntegrationEvent
    {
        return match ($event::class) {

            IdentityCreatedEvent::class =>
            new IdentityCreatedIntegrationEvent(
                $event->getIdentity()->getId()->value()
            ),

            default => throw new \LogicException("No IntegrationEvent for ".$event::class)
        };
    }

    public function supports(object $event): bool
    {
        return match ($event::class) {
            IdentityCreatedEvent::class => true,
            default => false
        };
    }
}
