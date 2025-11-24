<?php

namespace App\Tests\Utilities;

use MyLegoCollection\SharedEvent\IntegrationEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler(fromTransport: 'sync', priority: 10)]
class DummySyncHandler
{
    /**
     * @var array<IntegrationEvent>
     */
    public array $received = [];

    /**
     * @template T of IntegrationEvent
     * @param T $event
     * @return void
     */
    public function __invoke(IntegrationEvent $event): void
    {
        $this->received[] = $event;
    }

    /**
     * @return array<IntegrationEvent>
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
