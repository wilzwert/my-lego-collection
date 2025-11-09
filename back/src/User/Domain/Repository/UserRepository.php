<?php

namespace App\User\Domain\Repository;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;

interface UserRepository
{
    public function findByIdentityId(EntityId $identityId): ?User;

    public function findById(EntityId $userId): ?User;

    public function save(User $user): void;
}
