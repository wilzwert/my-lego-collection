<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\Local\Color;
use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\Part;

/**
 * @author Wilhelm Zwertvaegher
 */
interface ElementService
{
    /**
     * @param array<string, ExternalElement> $externalElements
     * @param array<string, Part> $parts with their externalId as key
     * @param array<string, Color> $colors with their externalId as key
     * @return array<string, Element> the created Element with their externalId as key
     */
    public function getOrCreateUnknownElements(array $externalElements, array $parts, array $colors): array;
}
