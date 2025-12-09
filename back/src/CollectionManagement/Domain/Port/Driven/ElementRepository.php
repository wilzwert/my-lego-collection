<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Element;
use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
interface ElementRepository
{
    public function findById(EntityId $id): ?Element;

    /**
     * @param array<EntityId> $externalIds
     * @return array<Element>
     */
    public function findByIds(array $ids): array;

    /**
     * @param array<string> $externalIds
     * @return array<Element>
     */
    public function findByExternalIds(array $externalIds): array;

    public function save(Element $element): void;

    public function saveAll(array $elements): void;

}
