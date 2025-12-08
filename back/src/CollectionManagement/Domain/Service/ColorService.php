<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\External\ExternalColor;
use App\CollectionManagement\Domain\Model\Local\Color;

/**
 * @author Wilhelm Zwertvaegher
 */
interface ColorService
{
    /**
     * @param array<string, ExternalColor> $externalColors with their externalId as key
     * @return array<string, Color> the created colors with their externalId as key
     */
    public function getOrCreateUnknownColors(array $externalColors): array;
}
