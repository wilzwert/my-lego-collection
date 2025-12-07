<?php

namespace App\CollectionManagement\Domain\Port\Driven;

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
