<?php

namespace App\CollectionManagement\Domain\Service;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveUserId
{
    public function getUserId(EntityId $identityId): ?EntityId;
}
