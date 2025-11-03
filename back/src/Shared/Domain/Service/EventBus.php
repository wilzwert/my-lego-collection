<?php

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Event\DomainEvent;

/**
 * @author W.Zwertvaegher
 * Domain Event Bus interface (i.e. Port)
 * This MUST be implemented by the infra to allow sending events from handlers on entities operations
 */

interface EventBus
{
    public function dispatch(DomainEvent $event): void;
}
