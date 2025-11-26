<?php

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Event\DomainEvent;

/**
 * @author W.Zwertvaegher
 * Domain Event Bus interface (i.e. Port)
 * This MUST be implemented by the infra to allow sending events from handlers on entities operations
 */

abstract class EventBus
{
    abstract public function dispatch(DomainEvent $event): void;

    /**
     * @param array<DomainEvent> $events
     * @return void
     */
    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
