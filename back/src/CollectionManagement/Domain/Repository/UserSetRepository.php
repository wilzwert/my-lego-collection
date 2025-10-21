<?php

namespace App\CollectionManagement\Domain\Repository;

use App\CollectionManagement\Domain\Model\UserSetCollection;

interface UserSetRepository
{
    public function findByUserAndExternalIds(string $userId, array $externalIds): UserSetCollection;
}
