<?php

namespace App\User\Domain\Port\Driven;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveUserForIdentity
{
    public function retrieveUser(EntityId $identityId): ?User;

}
