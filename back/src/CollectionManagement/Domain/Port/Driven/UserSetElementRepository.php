<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\Local\UserSetElement;
use App\CollectionManagement\Domain\Model\UserSetCollection;
use App\Shared\Domain\Model\EntityId;

interface UserSetElementRepository
{

    /**
     * @param EntityId $id
     * @return ?UserSet
     */
    public function findById(EntityId $id): ?UserSetElement;

    /**
     * @param EntityId $setId
     * @return array<UserSetElement>
     */
    public function findByUserSetId(EntityId $setId): array;

    public function save(UserSetElement $userSetElement): void;

    public function saveAll(array $userSetElements): void;
}
