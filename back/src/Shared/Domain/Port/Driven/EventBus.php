<?php

namespace App\Shared\Domain\Port\Driven;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Model\ProducesDomainEvents;

/**
 * @author W.Zwertvaegher
 * Domain Event Bus interface (i.e. Port)
 * This MUST be implemented by the infra to allow sending events from handlers on entities operations
 */

abstract class EventBus
{
    abstract public function dispatch(DomainEvent $event): void;

    /**
     * @param ProducesDomainEvents $aggregate
     * @return void
     */
    public function dispatchAll(ProducesDomainEvents $aggregate): void
    {
        foreach ($aggregate->pullEvents() as $event) {
            $this->dispatch($event);
        }
    }
}
