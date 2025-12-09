<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\UserSetCollection;
use App\Shared\Domain\Model\EntityId;

interface UserSetRepository
{

    /**
     * @param EntityId $id
     * @return ?UserSet
     */
    public function findById(EntityId $id): ?UserSet;

    /**
     * @param EntityId $userId
     * @return UserSetCollection
     */
    public function findByUserId(EntityId $userId): UserSetCollection;

    /**
     * @param Set $set
     * @return UserSetCollection
     */
    public function findIncompleteOwnedBySetId(EntityId $setId): UserSetCollection;

    public function save(UserSet $userSet): void;
}
