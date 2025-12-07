<?php

namespace App\User\Infrastructure\EventHandler;

use App\Shared\Infrastructure\EventHandler\CommandHandler;
use App\Shared\Infrastructure\EventHandler\MessageHandler;
use App\User\Application\Handler\CreateUserHandler;
use MyLegoCollection\SharedContracts\Command\CreateUserCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements CommandHandler<CreateUserCommand>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(private CreateUserHandler $createUserHandler)
    {
    }

    public static function getMessageHandled(): string
    {
        return CreateUserCommand::class;
    }

    public function __invoke(CreateUserCommand $command): void
    {
        ($this->createUserHandler)($command);
    }
}
