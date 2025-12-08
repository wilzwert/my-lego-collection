<?php

namespace App\CollectionManagement\Infrastructure\EventHandler;

use App\CollectionManagement\Application\Handler\CompleteUserSetHandler;
use App\Shared\Infrastructure\EventHandler\CommandHandler;
use MyLegoCollection\SharedContracts\Command\CompleteUserSetCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements CommandHandler<CompleteUserSetCommand>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class CompleteUserSetCommandHandler implements CommandHandler
{
    public function __construct(private CompleteUserSetHandler $completeUserSetHandler)
    {
    }

    public static function getMessageHandled(): string
    {
        return CompleteUserSetCommand::class;
    }

    public function __invoke(CompleteUserSetCommand $command): void
    {
        ($this->completeUserSetHandler)($command);
    }

}
