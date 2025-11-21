<?php

namespace App\Tests\Auth\Infrastructure\Security;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\Auth\Domain\Model\Identity;
use App\Auth\Infrastructure\Security\User\AuthenticatedUser;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AuthenticatedUserTest extends TestCase
{
    #[Test]
    public function getUserIdentifier_shouldReturnUserEmail(): void
    {
        $user = $this->createMock(Identity::class);
        $user->method('getEmail')->willReturn('john.doe@example.com');
        $user->method('getId')->willReturn(EntityId::fromString('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1'));

        $authenticatedUser = new AuthenticatedUser($user);

        self::assertSame('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1', $authenticatedUser->getUserIdentifier());
    }

    #[Test]
    public function getRoles_shouldReturnUserRoles(): void
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user = $this->createMock(Identity::class);
        $user->method('getRoles')->willReturn($roles);

        $authenticatedUser = new AuthenticatedUser($user);

        self::assertSame($roles, $authenticatedUser->getRoles());
    }

    #[Test]
    public function getPassword_shouldReturnPasswordHash(): void
    {
        $user = $this->createMock(Identity::class);
        $user->method('getPasswordHash')->willReturn('$2y$10$hashvalue');

        $authenticatedUser = new AuthenticatedUser($user);

        self::assertSame('$2y$10$hashvalue', $authenticatedUser->getPassword());
    }

    #[Test]
    public function eraseCredentials_shouldDoNothing(): void
    {
        $this->expectNotToPerformAssertions();
        $user = $this->createMock(Identity::class);
        $authenticatedUser = new AuthenticatedUser($user);

        // Just ensure it doesn't throw
        $authenticatedUser->eraseCredentials();
    }
}
