<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Color;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveColors
{
    /**
     * @param array $externalIds
     * @return array<Color>
     */
    public function byExternalIds(array $externalIds): array;

}
