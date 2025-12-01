<?php

namespace App\Shared\Infrastructure\Messenger;

use MyLegoCollection\SharedEvent\Event\IntegrationEvent;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Global shared bus for integration (i.e. cross-slices events)
 * @author Wilhelm Zwertvaegher
 */
readonly class IntegrationEventBus
{

    public function __construct(private MessageBusInterface $integrationBus)
    {
    }

    public function dispatch(IntegrationEvent $event): void
    {
        $this->integrationBus->dispatch($event);
    }
}
