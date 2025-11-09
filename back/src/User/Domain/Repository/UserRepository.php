<?php

namespace App\User\Domain\Repository;

use App\Shared\Domain\Model\Uuid;
use App\User\Domain\Model\User;

interface UserRepository
{
    public function findByIdentityId(Uuid $identityId): ?User;

    public function findById(Uuid $userId): ?User;

    public function save(User $user): void;
}
