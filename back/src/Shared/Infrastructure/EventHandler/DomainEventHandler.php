<?php

namespace App\Shared\Infrastructure\EventHandler;


use App\Shared\Domain\Event\DomainEvent;

/**
 * Interface that must be implemented by all event handlers in infrastructures
 * This will allow writing tests that check a specific mandatory event handler exists in a slice/module,
 * which can also be seen as self documentation / integrity check
 *
 * @author Wilhelm Zwertvaegher
 */
interface DomainEventHandler
{
    /**
     * @return class-string<DomainEvent> the DomainEvent class
     */
    public static function getEventHandled(): string;

}
