<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\External\ExternalColor;
use App\CollectionManagement\Domain\Model\Local\Color;
use App\CollectionManagement\Domain\Port\Driven\RetrieveColors;

/**
 * @author Wilhelm Zwertvaegher
 */
class DefaultColorService implements ColorService
{

    public function __construct(private readonly RetrieveColors $retrieveColors)
    {
    }

    /**
     * @param array<string, ExternalColor> $externalColors
     * @return array<string, Color> the created colors with their externalId as key
     */
    public function getOrCreateUnknownColors(array $externalColors): array
    {
        $localColors = [];
        foreach ($this->retrieveColors->byExternalIds(array_keys($externalColors)) as $color) {
            $localColors[$color->getExternalId()] = $color;
        }

        $colorsExternalIdsToCreate = array_diff(array_keys($externalColors), array_keys($localColors));

        foreach ($colorsExternalIdsToCreate as $externalId) {
            $externalColor = $externalColors[$externalId];
            $localColors[$externalColor->getExternalId()] = Color::create(
                $externalColor->getExternalId(),
                $externalColor->getLegoId(),
                $externalColor->getName(),
                $externalColor->getRgbCode()
            );
        }
        return $localColors;
    }
}
