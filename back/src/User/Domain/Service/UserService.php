<?php

namespace App\User\Domain\Service;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\User\Domain\Model\User;

interface UserService
{
    public function createUser(EntityId $identityId): ?User;
}
