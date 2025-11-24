<?php

namespace App\Auth\Infrastructure\Messenging;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Service\EventBus;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Auth local event bus for local auth slice related DomainEvent
 * @author Wilhelm Zwertvaegher
 */
class AuthEventBus implements EventBus
{
    public function __construct(private readonly MessageBusInterface $authBus)
    {

    }
    public function dispatch(DomainEvent $event): void
    {
        $this->authBus->dispatch($event);
    }
}
