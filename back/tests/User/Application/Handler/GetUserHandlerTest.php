<?php

namespace App\Tests\User\Application\Handler;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\User\Application\Command\GetUserQuery;
use App\User\Application\Handler\GetUserHandler;
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
        $expectedUser = $this->createMock(Identity::class);

        $userService = $this->createMock(IdentityService::class);
        $userService
            ->expects($this->once())
            ->method('getIdentityByIdentifier')
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

        $userService = $this->createMock(IdentityService::class);
        $userService
            ->expects($this->once())
            ->method('getIdentityByIdentifier')
            ->with('unknown-id')
            ->willReturn(null);

        $handler = new GetUserHandler($userService);

        $result = $handler($query);

        $this->assertNull($result);
    }
}
