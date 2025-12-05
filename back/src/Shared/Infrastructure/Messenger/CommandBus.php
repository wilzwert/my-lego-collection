<?php

namespace App\Shared\Infrastructure\Messenger;

use MyLegoCollection\SharedContracts\Command\Command;
use MyLegoCollection\SharedContracts\Event\IntegrationEvent;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Global bus for shared integration (i.e. cross-slices events)
 * @author Wilhelm Zwertvaegher
 */
readonly class CommandBus
{

    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
