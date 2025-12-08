<?php

namespace App\CollectionManagement\Infrastructure\EventHandler;

use App\CollectionManagement\Application\Handler\CompleteSetHandler;
use App\Shared\Infrastructure\EventHandler\CommandHandler;
use MyLegoCollection\SharedContracts\Command\CompleteSetCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements CommandHandler<CompleteSetCommand>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class CompleteSetCommandHandler implements CommandHandler
{

    public function __construct(private CompleteSetHandler $completeSetHandler)
    {
    }

    public static function getMessageHandled(): string
    {
        return CompleteSetCommand::class;
    }

    public function __invoke(CompleteSetCommand $command): void
    {
        ($this->completeSetHandler)($command);
    }
}
