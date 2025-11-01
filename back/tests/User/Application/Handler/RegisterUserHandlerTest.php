<?php

namespace App\Tests\User\Application\Handler;

use App\User\Application\Command\RegisterUserCommand;
use App\User\Application\Handler\RegisterUserHandler;
use App\User\Domain\Service\UserService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class RegisterUserHandlerTest extends TestCase
{
    #[Test]
    public function shouldInvokeUserServiceToCreateUser(): void
    {
        $command = new RegisterUserCommand('john@example.com', 'john_doe', 'password');
        $userService = $this->createMock(UserService::class);

        $userService
            ->expects($this->once())
            ->method('createUser')
            ->with($command);

        $handler = new RegisterUserHandler($userService);

        $handler($command);
    }
}
