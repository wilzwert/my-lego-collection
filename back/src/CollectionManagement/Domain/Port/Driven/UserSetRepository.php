<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\UserSetCollection;
use App\Shared\Domain\Model\EntityId;

interface UserSetRepository
{
    /**
     * @param EntityId $userId
     * @param list<string> $externalIds
     * @return UserSetCollection
     */
    public function findByUserAndExternalIds(EntityId $userId, array $externalIds): UserSetCollection;

    /**
     * @param Set $set
     * @return UserSetCollection
     */
    public function findIncompleteBySet(Set $set): UserSetCollection;

    public function save(UserSet $userSet): void;
}
