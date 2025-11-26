<?php

namespace App\Tests\User\Utilities;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserTestsUtility
{
    public static function generateUser(
        ?EntityId $userId = null,
        ?EntityId $entityId = null,
        ?\DateTimeImmutable   $createdAt = null,
        ?\DateTimeImmutable   $updatedAt = null
    ): User
    {
        return new User(
            $userId ?? EntityId::generate(),
            $entityId ?? EntityId::generate(),
            $createdAt ?? new \DateTimeImmutable(),
            $updatedAt ?? new \DateTimeImmutable()
        );
    }
}
