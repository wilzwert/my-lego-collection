<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\Local\Color;
use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\Part;
use App\CollectionManagement\Domain\Port\Driven\RetrieveElements;
use Override;

readonly class DefaultElementService implements ElementService
{
    public function __construct(
        private RetrieveElements $retrieveElements
    ) {
    }

    /**
     * @param array<string, ExternalElement> $externalElements
     * @param array<string, Part> $parts with their externalId as key
     * @param array<string, Color> $colors with their externalId as key
     * @return array<string, Element> the created Element with their externalId as key
     */
    #[Override]
    public function getOrCreateUnknownElements(array $externalElements, array $parts, array $colors): array
    {
        $localElements = [];
        foreach ($this->retrieveElements->byExternalIds(array_keys($externalElements)) as $element) {
            $localElements[$element->getExternalId()] = $element;
        }

        $elementsExternalIdsToCreate = array_diff(array_keys($externalElements), array_keys($localElements));

        foreach ($elementsExternalIdsToCreate as $externalId) {
            $externalElement = $externalElements[$externalId];
            $localElements[$externalElement->getExternalId()] = Element::create(
                $colors[$externalElement->getExternalColorId()]->getId(),
                $parts[$externalElement->getExternalPartId()]->getId(),
                $externalElement->getExternalId(),
                $parts[$externalElement->getExternalPartId()]->getName().' - '.$colors[$externalElement->getExternalColorId()]->getName(),
                $externalElement->getImagePath()
            );
        }

        return $localElements;
    }
}
