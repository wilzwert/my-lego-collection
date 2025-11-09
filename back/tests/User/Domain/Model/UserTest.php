<?php

namespace App\Tests\User\Domain\Model;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\File;
use App\Shared\Domain\Model\EntityId;
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

        $this->assertSame($this->id, $user->getId());
        $this->assertSame($this->identityId, $user->getIdentityId());
        $this->assertSame($now, $user->getCreatedAt());
        $this->assertSame($now, $user->getUpdatedAt());
        $this->assertNull($user->getAvatar());
    }


    #[Test]
    public function shouldSetAvatarAndUpdatedAt(): void
    {
        $fileId = EntityId::generate();
        $createdAt = new \DateTimeImmutable('2025-11-05T12:00:00');
        $user = new User($this->id, $this->identityId, $createdAt, $createdAt);
        $file = new File($fileId, 'ad123456.png', 'avatar.png', 'image/png', 'png', 'user.avatar', new \DateTimeImmutable('2025-11-07T12:00:00'));
        $newUser = $user->setAvatar($file);

        $this->assertNotSame($user, $newUser);
        $this->assertEquals($user->getId(), $newUser->getId());
        $this->assertEquals($user->getCreatedAt(), $newUser->getCreatedAt());
        $this->assertNotEquals($user->getUpdatedAt(), $newUser->getUpdatedAt());
        $this->assertEquals($file, $newUser->getAvatar());
    }
}
