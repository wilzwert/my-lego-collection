<?php

namespace App\Tests\User\Unit\Infrastructure\EventHandler;

use App\User\Application\Handler\CreateUserHandler;
use App\User\Infrastructure\EventHandler\CreateUserCommandHandler;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class CreateUserCommandHandlerTest extends TestCase
{
    #[Test]
    public function shouldHandleAuthIdentityCreatedEvent(): void
    {
        self::assertSame(CreateUserCommand::class, CreateUserCommandHandler::getMessageHandled());
    }

    #[Test]
    public function shouldCreateUser(): void
    {
        $handler = $this->createMock(CreateUserHandler::class);
        $handler->expects($this->once())->method('__invoke')->with(
            $this->callback(function (CreateUserCommand $command) {
                self::assertSame('identityId', $command->getIdentityId());
                return true;
            })
        );
        $eventHandler = new CreateUserCommandHandler($handler);
        $command = new CreateUserCommand('identityId');
        $eventHandler($command);
    }

}
