<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\Shared\Domain\Model\EntityId;

/**
 * @author W.Zwertvaegher
 */
interface SetRepository
{
    /**
     * @param Set $localSet
     * @return void
     */
    public function save(Set $localSet): void;


    /**
     * @param list<EntityId> $setIds
     * @param list<string> $externalIds
     * @return SetCollection
     */
    public function findByIdsAndExternalIds(array $setIds, array $externalIds): SetCollection;

    public function findByExternalId(string $externalId): ?Set;

    public function findById(EntityId $id): ?Set;

}
