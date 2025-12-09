<?php

namespace App\CollectionManagement\Application\Service;

use App\CollectionManagement\Domain\Model\External\ExternalSetElementCollectionProperties;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Port\Driven\ColorRepository;
use App\CollectionManagement\Domain\Port\Driven\ElementRepository;
use App\CollectionManagement\Domain\Port\Driven\PartRepository;
use App\CollectionManagement\Domain\Port\Driven\SetElementRepository;
use App\CollectionManagement\Domain\Service\ColorService;
use App\CollectionManagement\Domain\Service\ElementService;
use App\CollectionManagement\Domain\Service\PartService;
use App\CollectionManagement\Domain\Service\SetService;

/**
 * @author Wilhelm Zwertvaegher
 */
class CompleteSetService
{
    public function __construct(
        private readonly ElementService      $elementService,
        private readonly PartService         $partService,
        private readonly ColorService        $colorService,
        private readonly SetService          $setService,
        private readonly ColorRepository     $colorRepository,
        private readonly PartRepository      $partRepository,
        private readonly ElementRepository   $elementRepository,
        private readonly SetElementRepository   $setElementRepository
    ) {
    }

    public function completeSet(Set $set, ExternalSetElementCollectionProperties $properties): Set
    {
        // load / create all needed data: colors, parts, elements

        $colors = $this->colorService->getOrCreateUnknownColors($properties->getExternalColors());
        $this->colorRepository->saveAll($colors);

        $parts = $this->partService->getOrCreateUnknownParts($properties->getExternalParts());
        $this->partRepository->saveAll($parts);

        $elements = $this->elementService->getOrCreateUnknownElements($properties->getExternalElements(), $parts, $colors);
        $this->elementRepository->saveAll($elements);

        $setElements = $this->setService->createSetElements($set, $properties->getExternalSetElements(), $elements);
        $this->setElementRepository->saveAll($setElements);

        return $set->complete();
    }

}
