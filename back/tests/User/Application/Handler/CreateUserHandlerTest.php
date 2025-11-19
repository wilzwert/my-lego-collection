<?php

namespace App\Tests\User\Application\Handler;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Application\Handler\RegistrationHandler;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Model\EntityId;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Handler\CreateUserHandler;
use App\User\Domain\Service\UserService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class CreateUserHandlerTest extends TestCase
{
    #[Test]
    public function shouldInvokeUserServiceToCreateUser(): void
    {
        $identityId = EntityId::generate();
        $command = new CreateUserCommand($identityId->__toString());
        $userService = $this->createMock(UserService::class);

        $userService
            ->expects($this->once())
            ->method('createUser')
            ->with($identityId->__toString());

        $handler = new CreateUserHandler($userService);

        $handler($command);
    }
}
