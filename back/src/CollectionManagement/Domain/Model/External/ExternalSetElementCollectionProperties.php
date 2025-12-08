<?php

namespace App\CollectionManagement\Domain\Model\External;

/**
 * @author Wilhelm Zwertvaegher
 */
class ExternalSetElementCollectionProperties
{
    private $externalSetElements;

    private $externalElements;

    private $externalColors;

    private $externalParts;

    public function __construct(
        private readonly ExternalSetElementCollection $source
    ) {
    }

    private function loadFromSource(): void
    {
        $this->externalElements = $this->externalColors = $this->externalParts = [];
        foreach ($this->getExternalSetElements() as $externalSetElement) {
            $this->externalElements[$externalSetElement->getExternalElement()->getExternalId()] = $externalSetElement->getExternalElement();
            $this->externalColors[$externalSetElement->getExternalColor()->getExternalId()] = $externalSetElement->getExternalColor();
            $this->externalParts[$externalSetElement->getExternalPart()->getExternalId()] = $externalSetElement->getExternalPart();
        }
    }

    /**
     * @return array<string, ExternalSetElement>
     */
    public function getExternalSetElements(): array
    {
        if (!isset($this->externalSetElements)) {
            $this->externalSetElements = [];
            foreach ($this->source->toArray() as $externalSetElement) {
                $this->externalSetElements[$externalSetElement->getExternalId()] = $externalSetElement;
            }
        }
        return $this->externalSetElements;
    }

    /**
     * @return array<string, ExternalElement>
     */
    public function getExternalElements(): array
    {
        if (!isset($this->externalElements)) {
            $this->loadFromSource();
        }

        return $this->externalElements;
    }

    /**
     * @return array<string, ExternalPart>
     */
    public function getExternalParts(): array
    {
        if (!isset($this->externalParts)) {
            $this->loadFromSource();
        }

        return $this->externalParts;
    }

    /**
     * @return array<string, ExternalColor>
     */
    public function getExternalColors(): array
    {
        if (!isset($this->externalColors)) {
            $this->loadFromSource();
        }

        return $this->externalColors;
    }

}
