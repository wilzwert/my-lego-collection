<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
interface UserSetService
{

    public function createUserSet(EntityId $userId, Set $set): UserSet;

}
