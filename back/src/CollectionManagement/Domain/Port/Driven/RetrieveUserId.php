<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveUserId
{
    public function getUserId(EntityId $identityId): ?EntityId;
}
