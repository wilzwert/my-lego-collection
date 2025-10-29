<?php

namespace App\Tests\Auth;

/**
 * @author Wilhelm Zwertvaegher
 */
use App\Auth\AuthenticatedUser;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AuthenticatedUserTest extends TestCase
{
    #[Test]
    public function getUserIdentifier_shouldReturnUserEmail(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('john.doe@example.com');

        $authenticatedUser = new AuthenticatedUser($user);

        self::assertSame('john.doe@example.com', $authenticatedUser->getUserIdentifier());
    }

    #[Test]
    public function getRoles_shouldReturnUserRoles(): void
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn($roles);

        $authenticatedUser = new AuthenticatedUser($user);

        self::assertSame($roles, $authenticatedUser->getRoles());
    }

    #[Test]
    public function getPassword_shouldReturnPasswordHash(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getPasswordHash')->willReturn('$2y$10$hashvalue');

        $authenticatedUser = new AuthenticatedUser($user);

        self::assertSame('$2y$10$hashvalue', $authenticatedUser->getPassword());
    }

    #[Test]
    public function eraseCredentials_shouldDoNothing(): void
    {
        $this->expectNotToPerformAssertions();
        $user = $this->createMock(User::class);
        $authenticatedUser = new AuthenticatedUser($user);

        // Just ensure it doesn't throw
        $authenticatedUser->eraseCredentials();
    }

    #[Test]
    public function getDomainUser_shouldReturnOriginalUser(): void
    {
        $user = $this->createMock(User::class);
        $authenticatedUser = new AuthenticatedUser($user);

        self::assertSame($user, $authenticatedUser->getDomainUser());
    }
}
