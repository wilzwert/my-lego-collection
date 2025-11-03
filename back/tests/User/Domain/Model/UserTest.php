<?php

namespace App\Tests\User\Domain\Model;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class UserTest extends TestCase
{
    #[Test]
    public function shouldExposeAllGivenProperties(): void
    {
        $id = Uuid::fromString('123e4567-e89b-12d3-a456-426614174000');
        $email = 'john@example.com';
        $username = 'john_doe';
        $passwordHash = 'hashed-password';
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];

        $user = new Identity($id, $email, $username, $passwordHash, $roles);

        $this->assertSame($id, $user->getId());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($username, $user->getUsername());
        $this->assertSame($passwordHash, $user->getPasswordHash());
        $this->assertSame($roles, $user->getRoles());
    }

    #[Test]
    public function shouldHaveRoleUserByDefault(): void
    {
        $id = Uuid::fromString('123e4567-e89b-12d3-a456-426614174001');

        $user = new Identity($id, 'jane@example.com', 'jane_doe', 'secret-hash');

        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }
}
