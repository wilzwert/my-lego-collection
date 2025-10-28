<?php

namespace App\CollectionManagement\Domain\Repository;

use App\CollectionManagement\Domain\Model\UserSetCollection;

interface UserSetRepository
{
    /**
     * @param string $userId
     * @param list<string> $externalIds
     * @return UserSetCollection
     */
    public function findByUserAndExternalIds(string $userId, array $externalIds): UserSetCollection;
}
