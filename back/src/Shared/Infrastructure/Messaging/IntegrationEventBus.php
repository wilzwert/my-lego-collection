<?php

namespace App\Shared\Infrastructure\Messaging;

use MyLegoCollection\SharedEvent\IntegrationEvent;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Global bus for shared integration (i.e. cross-slices events)
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
