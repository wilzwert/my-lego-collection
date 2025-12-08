<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
class DefaultUserSetService implements UserSetService
{
    public function createUserSet(EntityId $userId, Set $set): UserSet
    {
        return UserSet::create(
            $userId,
            $set
        );
    }

}
