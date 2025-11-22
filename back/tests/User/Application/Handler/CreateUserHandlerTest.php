<?php

namespace App\Tests\User\Application\Handler;

use App\Shared\Domain\Model\EntityId;
use App\User\Application\Handler\IdentityCreatedHandler;
use App\User\Domain\Service\UserService;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
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
        $event = new IdentityCreatedIntegrationEvent($identityId->value());
        $userService = $this->createMock(UserService::class);

        $userService
            ->expects($this->once())
            ->method('createUser')
            ->with($identityId->__toString());

        $handler = new IdentityCreatedHandler($userService);

        $handler($event);
    }
}
