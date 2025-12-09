<?php

namespace App\Tests\Utilities;

use MyLegoCollection\SharedContracts\Event\IntegrationEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @template T of IntegrationEvent
 * @implements DummyHandler<T>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler(fromTransport: 'sync', priority: 10)]
class DummySyncIntegrationEventHandler implements DummyHandler
{
    /**
     * @var array<T>
     */
    public array $received = [];

    /**
     * @param T $event
     * @return void
     */
    public function __invoke(IntegrationEvent $event): void
    {
        fwrite(STDERR, serialize($event).PHP_EOL);
        $this->received[] = $event;
    }

    /**
     * @return array<T>
     */
    public function getReceivedMessages(): array
    {
        return $this->received;
    }

    public function reset(): void
    {
        $this->received = [];
    }
}
