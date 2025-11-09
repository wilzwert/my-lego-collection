<?php

namespace App\Tests\Auth;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Infrastructure\Security\AuthenticatedUser;
use App\Auth\Infrastructure\Security\UserProvider;
use App\Shared\Domain\Model\EntityId;
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
        $domainIdentity = $this->createMock(Identity::class);
        $domainIdentity->method('getId')->willReturn(EntityId::fromString('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1'));
        $repository = $this->createMock(IdentityRepository::class);
        $repository->method('findByIdentifier')->willReturn($domainIdentity);

        $provider = new UserProvider($repository);

        $result = $provider->loadUserByIdentifier('john.doe@example.com');

        self::assertInstanceOf(AuthenticatedUser::class, $result);
        self::assertSame('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1', $result->getUserIdentifier());
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
