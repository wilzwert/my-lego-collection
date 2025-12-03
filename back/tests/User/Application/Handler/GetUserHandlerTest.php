<?php

namespace App\Tests\User\Application\Handler;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use App\Tests\User\Utilities\UserTestsUtility;
use App\User\Application\Command\GetUserByIdentityQuery;
use App\User\Application\Handler\GetUserHandler;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\UserRepository;
use App\User\Domain\Service\UserService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class GetUserHandlerTest extends TestCase
{
    #[Test]
    public function returnsUserFromRepository(): void
    {
        $query = new GetUserByIdentityQuery('a1a1a1a1-a1a1-41a1-a1a1-a1a1a1a1a1a1');
        $expectedUser = UserTestsUtility::generateUser();

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with('a1a1a1a1-a1a1-41a1-a1a1-a1a1a1a1a1a1')
            ->willReturn($expectedUser);

        $handler = new GetUserHandler($userRepository);

        $result = $handler($query);

        self::assertSame($expectedUser, $result);
    }

    #[Test]
    public function returnsNullIfUserNotFound(): void
    {
        $query = new GetUserByIdentityQuery('a1a1a1a1-a1a1-41a1-a1a1-a1a1a1a1a1a1');

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with('a1a1a1a1-a1a1-41a1-a1a1-a1a1a1a1a1a1')
            ->willReturn(null);

        $handler = new GetUserHandler($userRepository);

        $result = $handler($query);

        self::assertNull($result);
    }
}
