<?php

namespace App\Tests\User\Application\Handler;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\Tests\User\Domain\Model\UserTest;
use App\Tests\User\Utilities\UserTestsUtility;
use App\User\Application\Handler\CreateUserHandler;
use App\User\Domain\Service\UserService;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
use MyLegoCollection\SharedEvent\Event\IdentityCreatedIntegrationEvent;
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
        $user = UserTestsUtility::generateUser(identityId: $identityId);
        $command = new CreateUserCommand($identityId->value());
        $userService = $this->createMock(UserService::class);
        $eventBus = $this->createMock(EventBus::class);

        $userService
            ->expects($this->once())
            ->method('createUser')
            ->with($identityId->__toString())
            ->willReturn($user);

        $eventBus
            ->expects($this->once())
            ->method('dispatchAll')
            ->with($user);

        $handler = new CreateUserHandler($userService, $eventBus);

        $handler($command);
    }
}
