<?php

namespace App\Tests\Auth\Domain\Model;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class IdentityTest extends TestCase
{
    #[Test]
    public function shouldExposeAllGivenProperties(): void
    {
        $id = EntityId::fromString('123e4567-e89b-42d3-9456-426614174000');
        $email = 'john@example.com';
        $username = 'john_doe';
        $passwordHash = 'hashed-password';
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];

        $user = new Identity($id, $email, $username, $passwordHash, $roles);

        self::assertSame($id, $user->getId());
        self::assertSame($email, $user->getEmail());
        self::assertSame($username, $user->getUsername());
        self::assertSame($passwordHash, $user->getPasswordHash());
        self::assertSame($roles, $user->getRoles());
    }

    #[Test]
    public function shouldHaveRoleUserByDefault(): void
    {
        $id = EntityId::fromString('123e4567-e89b-42d3-be45-426614174001');

        $user = new Identity($id, 'jane@example.com', 'jane_doe', 'secret-hash');

        self::assertSame(['ROLE_USER'], $user->getRoles());
    }
}
