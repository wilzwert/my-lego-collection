<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Part;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveParts
{
    /**
     * @param array $externalIds
     * @return array<Part>
     */
    public function byExternalIds(array $externalIds): array;

}
