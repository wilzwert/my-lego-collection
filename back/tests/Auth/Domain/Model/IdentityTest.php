<?php

namespace App\Tests\Auth\Domain\Model;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Domain\Model\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

/**
 * @author Wilhelm Zwertvaegher
 */

final class IdentityTest extends TestCase
{
    #[Test]
    public function whenEmailIsInvalid_thenShouldThrowValidationException(): void
    {
        $this->expectException(ValidationException::class);
        $identity = new Identity(
            Uuid::generate(),
            'invalid email',
            'username',
            'passwordHash',
            ['ROLE_USER']
        );
    }

    #[Test]
    public function whenNoRole_thenShouldThrowValidationException(): void
    {
        $this->expectException(ValidationException::class);
        $identity = new Identity(
            Uuid::generate(),
            'email@example.com',
            'username',
            'passwordHash',
            []

        );
    }

    #[Test]
    public function shouldExposeAllGivenProperties(): void
    {
        $id = Uuid::fromString('dec59684-bdef-4a63-bad4-591c35540fa8');
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
        $id = Uuid::fromString('dec59684-bdef-4a63-bad4-591c35540fa8');

        $user = new Identity($id, 'jane@example.com', 'jane_doe', 'secret-hash');

        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }
}
