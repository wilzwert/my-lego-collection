<?php

namespace App\CollectionManagement\Domain\Model\External;

use App\Shared\Domain\Model\Collection;

/**
 * @author Wilhelm Zwertvaegher
 * @extends Collection<ExternalSetElement>
 */
final class ExternalSetElementCollection extends Collection
{
    public function __construct(array $elements = [])
    {
        parent::__construct(ExternalSetElement::class, $elements);
    }

    /**
     * @return list<array<string, object>>
     */
    public function toMap(): array
    {
        $externalSetElements = $externalElements = $externalColors = $externalParts = [];
        foreach ($this->toArray() as $externalSetElement) {
            $externalSetElements[$externalSetElement->getExternalId()] = $externalSetElement;
            $externalElements[$externalSetElement->getExternalId()] = $externalSetElement;
            $externalColors[$externalSetElement->getExternalColor()->getExternalId()] = $externalSetElement->getExternalColor();
            $externalParts[$externalSetElement->getExternalPart()->getExternalId()] = $externalSetElement->getExternalPart();
        }
        return [$externalSetElements, $externalElements, $externalColors, $externalParts];
    }

    public function toData(): ExternalSetElementCollectionProperties
    {
        return new ExternalSetElementCollectionProperties($this);
    }


}
