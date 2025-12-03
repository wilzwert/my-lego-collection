<?php

namespace App\Auth\Infrastructure\Messenger;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Port\Driven\EventBus;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Auth local event bus for local auth slice related DomainEvent
 * @author Wilhelm Zwertvaegher
 */
class AuthEventBus extends EventBus
{
    public function __construct(private readonly MessageBusInterface $authBus)
    {

    }
    public function dispatch(DomainEvent $event): void
    {
        $this->authBus->dispatch($event);
    }
}
