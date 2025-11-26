<?php

namespace App\Shared\Infrastructure\EventHandler;


use MyLegoCollection\SharedEvent\Event\IntegrationEvent;

/**
 * Interface that must be implemented by all event handlers in infrastructures
 * This will allow writing tests that check a specific mandatory event handler exists in a slice/module,
 * which can also be seen as self documentation / integrity check
 *
 * @author Wilhelm Zwertvaegher
 */
interface IntegrationEventHandler
{
    /**
     * @return class-string<IntegrationEvent> the event class
     */
    public static function getEventHandled(): string;

}
