<?php

namespace App\CollectionManagement\Domain\Service;

interface LegoDataLoader
{
    public function findSets(string $search): array;

    public function findParts(string $search): array;
}
