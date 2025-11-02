<?php

namespace App\Tests\Auth;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Infrastructure\Security\AuthenticatedUser;
use App\Auth\Infrastructure\Security\UserProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Wilhelm Zwertvaegher
 */

final class UserProviderTest extends TestCase
{
    #[Test]
    public function loadUserByIdentifier_shouldReturnAuthenticatedUserWhenFound(): void
    {
        $domainUser = $this->createMock(Identity::class);
        $repository = $this->createMock(IdentityRepository::class);
        $repository->method('findByIdentifier')->willReturn($domainUser);

        $provider = new UserProvider($repository);

        $result = $provider->loadUserByIdentifier('john.doe@example.com');

        self::assertInstanceOf(AuthenticatedUser::class, $result);
        self::assertSame($domainUser, $result->getDomainIdentity());
    }

    #[Test]
    public function loadUserByIdentifier_shouldThrowExceptionWhenUserNotFound(): void
    {
        $repository = $this->createMock(IdentityRepository::class);
        $repository->method('findByIdentifier')->willReturn(null);

        $provider = new UserProvider($repository);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("Identity 'unknown@example.com' not found.");

        $provider->loadUserByIdentifier('unknown@example.com');
    }

    #[Test]
    public function refreshUser_shouldReturnSameInstance(): void
    {
        $repository = $this->createMock(IdentityRepository::class);
        $provider = new UserProvider($repository);

        $user = $this->createMock(UserInterface::class);

        $result = $provider->refreshUser($user);

        self::assertSame($user, $result);
    }

    #[Test]
    public function supportsClass_shouldReturnTrueForAuthenticatedUserClass(): void
    {
        $repository = $this->createMock(IdentityRepository::class);
        $provider = new UserProvider($repository);

        self::assertTrue($provider->supportsClass(AuthenticatedUser::class));
    }

    #[Test]
    public function supportsClass_shouldReturnFalseForDifferentClass(): void
    {
        $repository = $this->createMock(IdentityRepository::class);
        $provider = new UserProvider($repository);

        self::assertFalse($provider->supportsClass(\stdClass::class));
    }
}
