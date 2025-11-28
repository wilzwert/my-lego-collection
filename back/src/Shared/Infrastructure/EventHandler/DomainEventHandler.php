<?php

namespace App\Shared\Infrastructure\EventHandler;


use App\Shared\Domain\Event\DomainEvent;

/**
 * @template T of DomainEvent
 * @extends MessageHandler<T>
 * Interface that must be implemented by all domain event handlers in infrastructures
 * This will allow writing tests that check a specific mandatory event handler exists in a slice/module,
 * which can also be seen as self documentation / integrity check
 *
 * @author Wilhelm Zwertvaegher
 */
interface DomainEventHandler extends MessageHandler
{

}
