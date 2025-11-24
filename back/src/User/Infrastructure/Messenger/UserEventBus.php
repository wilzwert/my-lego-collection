<?php

namespace App\User\Infrastructure\Messenger;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Service\EventBus;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Auth local event bus for local auth slice related DomainEvent
 * @author Wilhelm Zwertvaegher
 */
class UserEventBus implements EventBus
{
    public function __construct(private readonly MessageBusInterface $userBus)
    {

    }

    public function dispatch(DomainEvent $event): void
    {
        $this->userBus->dispatch($event);
    }
}
