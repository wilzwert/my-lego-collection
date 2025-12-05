<?php

namespace App\Tests\Utilities;

use MyLegoCollection\SharedContracts\Command\Command;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @template T of Command
 * @implements DummyHandler<T>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler(fromTransport: 'sync', priority: 10)]
class DummySyncCommandHandler implements DummyHandler
{
    /**
     * @var array<T>
     */
    public array $received = [];

    /**
     * @param T $command
     * @return void
     */
    public function __invoke(Command $command): void
    {
        $this->received[] = $command;
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
