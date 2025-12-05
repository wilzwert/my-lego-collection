<?php

namespace App\Shared\Infrastructure\EventHandler;


use MyLegoCollection\SharedContracts\Event\IntegrationEvent;

/**
 * @template T of IntegrationEvent
 * @extends MessageHandler<T>
 * Interface that must be implemented by all integration event handlers in infrastructures
 * This will allow writing tests that check a specific mandatory event handler exists in a slice/module,
 * which can also be seen as self documentation / integrity check
 *
 * @author Wilhelm Zwertvaegher
 */
interface IntegrationEventHandler extends MessageHandler
{

}
