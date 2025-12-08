<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Color;
use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
interface ColorRepository
{
    public function findById(EntityId $id): ?Color;

    /**
     * @param array<string> $externalIds
     * @return array<Color>
     */
    public function findByExternalIds(array $externalIds): array;

    public function save(Color $color): void;

    /**
     * @param array<Color> $colors
     * @return void
     */
    public function saveAll(array $colors): void;

}
