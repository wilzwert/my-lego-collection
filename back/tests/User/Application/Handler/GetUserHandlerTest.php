<?php

namespace App\Tests\User\Application\Handler;

use App\User\Application\Handler\GetUserHandler;
use App\User\Application\Command\GetUserQuery;
use App\User\Domain\Entity\User;
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
        $query = new GetUserQuery('user-123');
        $expectedUser = $this->createMock(User::class);

        $userService = $this->createMock(UserService::class);
        $userService
            ->expects($this->once())
            ->method('getUserByIdentifier')
            ->with('user-123')
            ->willReturn($expectedUser);

        $handler = new GetUserHandler($userService);

        $result = $handler($query);

        $this->assertSame($expectedUser, $result);
    }

    #[Test]
    public function returnsNullIfUserNotFound(): void
    {
        $query = new GetUserQuery('unknown-id');

        $userService = $this->createMock(UserService::class);
        $userService
            ->expects($this->once())
            ->method('getUserByIdentifier')
            ->with('unknown-id')
            ->willReturn(null);

        $handler = new GetUserHandler($userService);

        $result = $handler($query);

        $this->assertNull($result);
    }
}
