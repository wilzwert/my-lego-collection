<?php

namespace App\Tests\User\Utilities;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\User\Domain\Model\User;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserTestsUtility
{
    public static function generateUser(
        ?EntityId $userId = null,
        ?EntityId $identityId = null,
        ?\DateTimeImmutable   $createdAt = null,
        ?\DateTimeImmutable   $updatedAt = null,
        ?StoredFile $avatar = null
    ): User
    {
        return new User(
            $userId ?? EntityId::generate(),
            $identityId ?? EntityId::generate(),
            $createdAt ?? new \DateTimeImmutable(),
            $updatedAt ?? new \DateTimeImmutable(),
            $avatar
        );
    }
}
