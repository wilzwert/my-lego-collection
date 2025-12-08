<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Element;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveElements
{
    /**
     * @param array $externalIds
     * @return array<Element>
     */
    public function byExternalIds(array $externalIds): array;

}
