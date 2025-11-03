<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Service\EventBus;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class SymfonyEventBus implements EventBus
{

    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function dispatch(DomainEvent $event): void
    {
        $this->messageBus->dispatch($event);
    }
}
