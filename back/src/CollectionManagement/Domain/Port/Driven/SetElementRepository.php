<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
interface SetElementRepository
{
    public function save(SetElement $setElement): void;

    /**
     * @param array<SetElement> $setElements
     * @return void
     */
    public function saveAll(array $setElements): void;

}
