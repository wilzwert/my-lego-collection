<?php

namespace App\Shared\Domain\Event;

/**
 * Interface that must be implemented by all domain event handlers in infrastructures
 * This will allow writing tests that check a specific mandatory event handler exists in a slice/module,
 * which can also be seen as self documentation / integrity check
 *
 * @author Wilhelm Zwertvaegher
 */
interface DomainEventHandler
{
    /**
     * @return string the event name service.eventName
     */
    public static function getEventHandled(): string;

    public function handle(DomainEvent $event): void;
}
