<?php

namespace App\Tests\User\Unit\Domain\Model;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\User\Domain\Event\AvatarUpdatedEvent;
use App\User\Domain\Model\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class UserTest extends TestCase
{

    private EntityId $id;
    private EntityId $identityId;

    protected function setUp(): void
    {
        $this->id = EntityId::fromString('123e4567-e89b-42d3-a456-426614174000');
        $this->identityId = EntityId::fromString('87654321-e89b-42d3-a456-426614174000');
    }

    #[Test]
    public function shouldExposeAllGivenProperties(): void
    {
        $now = new \DateTimeImmutable();
        $user = new User($this->id, $this->identityId, $now, $now);

        self::assertSame($this->id, $user->getId());
        self::assertSame($this->identityId, $user->getIdentityId());
        self::assertSame($now, $user->getCreatedAt());
        self::assertSame($now, $user->getUpdatedAt());
        self::assertNull($user->getAvatar());
    }


    #[Test]
    public function shouldSetAvatar(): void
    {
        $fileId = EntityId::generate();
        $createdAt = new \DateTimeImmutable('2025-11-05T12:00:00');
        $user = new User($this->id, $this->identityId, $createdAt, $createdAt);
        $file = new StoredFile($fileId, 'ad123456.png', 'avatar.png', 'image/png', 'png', 'user.avatar', new \DateTimeImmutable('2025-11-07T12:00:00'));
        $newUser = $user->setAvatar($file);

        self::assertNotSame($user, $newUser);
        self::assertEquals($user->getId(), $newUser->getId());
        self::assertEquals($user->getCreatedAt(), $newUser->getCreatedAt());
        self::assertNotEquals($user->getUpdatedAt(), $newUser->getUpdatedAt());
        self::assertEquals($file, $newUser->getAvatar());
        $events = $newUser->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(AvatarUpdatedEvent::class, $events[0]);
        self::assertSame($newUser, $events[0]->getUser());
    }

    #[Test]
    public function shouldDeleteAvatar(): void
    {
        $fileId = EntityId::generate();
        $createdAt = new \DateTimeImmutable('2025-11-05T12:00:00');
        $file = new StoredFile($fileId, 'ad123456.png', 'avatar.png', 'image/png', 'png', 'user.avatar', new \DateTimeImmutable('2025-11-07T12:00:00'));
        $user = new User($this->id, $this->identityId, $createdAt, $createdAt, $file);

        $newUser = $user->setAvatar(null);

        self::assertNotSame($user, $newUser);
        self::assertEquals($user->getId(), $newUser->getId());
        self::assertEquals($user->getCreatedAt(), $newUser->getCreatedAt());
        self::assertNotEquals($user->getUpdatedAt(), $newUser->getUpdatedAt());
        self::assertNull($newUser->getAvatar());
        $events = $newUser->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(AvatarUpdatedEvent::class, $events[0]);
        self::assertSame($newUser, $events[0]->getUser());
    }
}
