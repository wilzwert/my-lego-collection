<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\Local\Part;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Port\Driven\RetrieveParts;
use App\Shared\Domain\Model\EntityId;
use Override;

readonly class DefaultPartService implements PartService
{
    public function __construct(
        private LegoDataProvider $legoDataProvider,
        private RetrieveParts $retrieveParts
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findParts(string $search, ?EntityId $userId = null): PartCollection
    {
        // get parts from data provider
        return $this->legoDataProvider->findParts($search);
    }

    /**
     * @param array<string, ExternalPart> $externalParts with their externalId as key
     * @return array<string, Part> the created Parts with their externalId as key
     */
    #[Override]
    public function getOrCreateUnknownParts(array $externalParts): array
    {
        $localParts = [];
        foreach ($this->retrieveParts->byExternalIds(array_keys($externalParts)) as $part) {
            $localParts[$part->getExternalId()] = $part;
        }

        $partsExternalIdsToCreate = array_diff(array_keys($externalParts), array_keys($localParts));

        foreach ($partsExternalIdsToCreate as $externalId) {
            $externalPart = $externalParts[$externalId];
            $localParts[$externalPart->getExternalId()] = Part::create(
                $externalPart->getExternalId(),
                $externalPart->getLegoId(),
                $externalPart->getName(),
                $externalPart->getImagePath()
            );
        }
        return $localParts;
    }
}
