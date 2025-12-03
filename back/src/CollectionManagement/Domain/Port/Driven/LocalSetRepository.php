<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\SetCollection;

/**
 * @author W.Zwertvaegher
 */
interface LocalSetRepository
{
    /**
     * @param Set $localSet
     * @return void
     */
    public function add(Set $localSet): void;

    /**
     * @param Set $localSet
     * @return void
     */
    public function update(Set $localSet): void;

    /**
     * @param string $userId
     * @param list<string> $externalIds
     * @return SetCollection
     */
    public function findByUserAndExternalIds(string $userId, array $externalIds): SetCollection;

}
