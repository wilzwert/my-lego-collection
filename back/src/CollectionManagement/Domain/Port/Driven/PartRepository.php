<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Part;
use App\Shared\Domain\Model\EntityId;

interface PartRepository
{
    public function findById(EntityId $id): ?Part;

    /**
     * @param array<string> $externalIds
     * @return array<Part>
     */
    public function findByExternalIds(array $externalIds): array;

    public function save(Part $part): void;

    public function saveAll(array $parts): void;

}
