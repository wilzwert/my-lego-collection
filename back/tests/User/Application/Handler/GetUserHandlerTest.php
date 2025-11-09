<?php

namespace App\Tests\User\Application\Handler;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\User\Application\Command\GetUserByIdentityQuery;
use App\User\Application\Handler\GetUserHandler;
use App\User\Domain\Model\User;
use App\User\Domain\Service\UserService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class GetUserHandlerTest extends TestCase
{
    #[Test]
    public function returnsUserFromService(): void
    {
        $query = new GetUserByIdentityQuery('a1a1a1a1-a1a1-41a1-a1a1-a1a1a1a1a1a1');
        $expectedUser = $this->createMock(User::class);

        $userService = $this->createMock(UserService::class);
        $userService
            ->expects($this->once())
            ->method('getUserByIdentityId')
            ->with('a1a1a1a1-a1a1-41a1-a1a1-a1a1a1a1a1a1')
            ->willReturn($expectedUser);

        $handler = new GetUserHandler($userService);

        $result = $handler($query);

        $this->assertSame($expectedUser, $result);
    }

    #[Test]
    public function returnsNullIfUserNotFound(): void
    {
        $query = new GetUserByIdentityQuery('a1a1a1a1-a1a1-41a1-a1a1-a1a1a1a1a1a1');

        $userService = $this->createMock(UserService::class);
        $userService
            ->expects($this->once())
            ->method('getUserByIdentityId')
            ->with('a1a1a1a1-a1a1-41a1-a1a1-a1a1a1a1a1a1')
            ->willReturn(null);

        $handler = new GetUserHandler($userService);

        $result = $handler($query);

        $this->assertNull($result);
    }
}
