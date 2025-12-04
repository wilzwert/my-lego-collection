<?php

namespace App\Tests\Auth\Unit\Infrastructure\Security;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Port\Driven\IdentityRepository;
use App\Auth\Infrastructure\Security\User\AuthenticatedUser;
use App\Auth\Infrastructure\Security\UserProvider;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Wilhelm Zwertvaegher
 */

final class UserProviderTest extends TestCase
{
    private UserProvider $userProvider;


    #[Test]
    public function loadUserByIdentifier_shouldReturnAuthenticatedUserWhenFound(): void
    {
        $domainIdentity = new Identity(
            EntityId::fromString('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1'),
            'test@example.com',
            'test',
            'hash'
        );

        $repository = $this->createMock(IdentityRepository::class);
        $repository->method('findById')->willReturn($domainIdentity);
        $logger = $this->createMock(LoggerInterface::class);

        $provider = new UserProvider($repository, $logger);

        $result = $provider->loadUserByIdentifier('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1');

        self::assertInstanceOf(AuthenticatedUser::class, $result);
        self::assertSame('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1', $result->getUserIdentifier());
    }

    #[Test]
    public function loadUserByIdentifier_shouldLoadByIdentifier_whenIdentityInvalid(): void
    {
        $domainIdentity = new Identity(
            EntityId::fromString('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1'),
            'test@example.com',
            'test',
            'hash'
        );

        $repository = $this->createMock(IdentityRepository::class);
        $repository->expects($this->never())->method('findById');
        $repository->expects($this->once())
            ->method('findByIdentifier')
            ->with()
            ->willReturn($domainIdentity);
        $logger = $this->createMock(LoggerInterface::class);

        $provider = new UserProvider($repository, $logger);

        $result = $provider->loadUserByIdentifier('test@example.com');
        self::assertInstanceOf(AuthenticatedUser::class, $result);
        self::assertSame('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1', $result->getUserIdentifier());

    }

    #[Test]
    public function loadUserByIdentifier_shouldThrowExceptionWhenUserNotFound(): void
    {
        $repository = $this->createMock(IdentityRepository::class);
        $repository->method('findById')->willReturn(null);
        $logger = $this->createMock(LoggerInterface::class);

        $provider = new UserProvider($repository, $logger);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("Identity 'a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1' not found.");

        $provider->loadUserByIdentifier('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1');
    }

    #[Test]
    public function refreshUser_shouldReturnSameInstance(): void
    {
        $repository = $this->createMock(IdentityRepository::class);
        $logger = $this->createMock(LoggerInterface::class);
        $provider = new UserProvider($repository, $logger);

        $user = $this->createMock(UserInterface::class);

        $result = $provider->refreshUser($user);

        self::assertSame($user, $result);
    }

    #[Test]
    public function supportsClass_shouldReturnTrueForAuthenticatedUserClass(): void
    {
        $repository = $this->createMock(IdentityRepository::class);
        $logger = $this->createMock(LoggerInterface::class);
        $provider = new UserProvider($repository, $logger);

        self::assertTrue($provider->supportsClass(AuthenticatedUser::class));
    }

    #[Test]
    public function supportsClass_shouldReturnFalseForDifferentClass(): void
    {
        $repository = $this->createMock(IdentityRepository::class);
        $logger = $this->createMock(LoggerInterface::class);
        $provider = new UserProvider($repository, $logger);

        self::assertFalse($provider->supportsClass(\stdClass::class));
    }
}
