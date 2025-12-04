<?php

namespace App\Tests\Auth\Unit\Infrastructure\Security;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\Auth\Infrastructure\Security\User\AuthenticatedUser;
use App\Shared\Domain\Model\EntityId;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AuthenticatedUserTest extends TestCase
{
    #[Test]
    public function getUserIdentifier_shouldReturnUserEmail(): void
    {
        $identity = AuthTestsUtility::generateIdentity(EntityId::fromString('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1'));

        $authenticatedUser = new AuthenticatedUser($identity);

        self::assertSame('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1', $authenticatedUser->getUserIdentifier());
    }

    #[Test]
    public function getRoles_shouldReturnUserRoles(): void
    {
        $identity = AuthTestsUtility::generateIdentity(roles: ['ROLE_USER', 'ROLE_ADMIN']);

        $authenticatedUser = new AuthenticatedUser($identity);

        self::assertSame(['ROLE_USER', 'ROLE_ADMIN'], $authenticatedUser->getRoles());
    }

    #[Test]
    public function getPassword_shouldReturnPasswordHash(): void
    {
        $identity = AuthTestsUtility::generateIdentity(passwordHash: '$2y$10$hashvalue');

        $authenticatedUser = new AuthenticatedUser($identity);

        self::assertSame('$2y$10$hashvalue', $authenticatedUser->getPassword());
    }

    #[Test]
    public function eraseCredentials_shouldDoNothing(): void
    {
        $this->expectNotToPerformAssertions();

        $identity = AuthTestsUtility::generateIdentity();
        $authenticatedUser = new AuthenticatedUser($identity);

        // Just ensure it doesn't throw
        $authenticatedUser->eraseCredentials();
    }
}
