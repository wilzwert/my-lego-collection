<?php

namespace App\User\Infrastructure\EventHandler;

use App\Shared\Infrastructure\EventHandler\CommandHandler;
use App\User\Application\Handler\CreateUserHandler;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler(fromTransport: 'sync', priority: 10)]
readonly class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(private CreateUserHandler $createUserHandler)
    {
    }


    public static function getCommandHandled(): string
    {
        return CreateUserCommand::class;
    }

    public function __invoke(CreateUserCommand $command): void
    {
        ($this->createUserHandler)($command);
    }
}
