<?php

namespace App\Tests\Auth\Unit\Domain\Model;

use App\Auth\Domain\Event\IdentityCompletedEvent;
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

        $identity = new Identity($id, $email, $username, $passwordHash, $roles);

        self::assertSame($id, $identity->getId());
        self::assertSame($email, $identity->getEmail());
        self::assertSame($username, $identity->getUsername());
        self::assertSame($passwordHash, $identity->getPasswordHash());
        self::assertSame($roles, $identity->getRoles());
    }

    #[Test]
    public function shouldHaveRoleUserByDefault(): void
    {
        $id = EntityId::fromString('123e4567-e89b-42d3-be45-426614174001');

        $identity = new Identity($id, 'jane@example.com', 'jane_doe', 'secret-hash');

        self::assertSame(['ROLE_USER'], $identity->getRoles());
    }

    #[Test]
    public function shouldCompleteIdentity(): void
    {
        $id = EntityId::fromString('123e4567-e89b-42d3-be45-426614174001');
        $identity = new Identity($id, 'jane@example.com', 'jane_doe', 'secret-hash');

        self::assertFalse($identity->isComplete());

        $completedIdentity = $identity->complete();
        self::assertTrue($completedIdentity->isComplete());
        // check defensive copying
        self::assertNotSame($identity, $completedIdentity);
        $events = $completedIdentity->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(IdentityCompletedEvent::class, $events[0]);
        self::assertSame($completedIdentity, $events[0]->getIdentity());
    }

    #[Test]
    public function complete_shouldDoNothing_whenAlreadyComplete(): void
    {
        $id = EntityId::fromString('123e4567-e89b-42d3-be45-426614174001');
        $identity = new Identity(
            id: $id,
            email: 'jane@example.com',
            username: 'jane_doe',
            passwordHash: 'secret-hash',
            isComplete: true
        );

        self::assertTrue($identity->isComplete());

        $completedIdentity = $identity->complete();
        self::assertSame($identity, $completedIdentity);
        $events = $completedIdentity->pullEvents();
        self::assertCount(0, $events);
    }
}
