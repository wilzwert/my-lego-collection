<?php

namespace App\CollectionManagement\Application\Service;

use App\CollectionManagement\Domain\Model\EnrichedSet;
use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Port\Driven\SetRepository;
use App\CollectionManagement\Domain\Port\Driven\UserSetRepository;
use App\CollectionManagement\Domain\Service\LegoDataProvider;
use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
class FindSetsService
{

    public function __construct(
        private readonly LegoDataProvider $legoDataProvider,
        private readonly SetRepository $setRepository,
        private readonly UserSetRepository $userSetRepository
    ) {
    }

    /**
     * Find sets by a search string
     * The resulting array should contain a list of sets, enriched with current user info if available
     * The Service should merge external sets and current user's local sets if available
     *
     * @param string $search the string to search (set id, part of set title)
     * @param EntityId|null $userId the user's id if available
     * @return EnrichedSetCollection
     */
    public function findSets(string $search, ?EntityId $userId = null): EnrichedSetCollection
    {
        // get sets from external data provider
        $externalSets = $this->legoDataProvider->findSets($search);
        // if current user is not set, then we can return found data as is
        if ($userId === null) {
            return new EnrichedSetCollection(
                array_map(fn ($set) => new EnrichedSet($set, null), $externalSets->toArray())
            );
        }

        if(!count($userSets = $this->userSetRepository->findByUserId($userId)->toArray())) {
            return new EnrichedSetCollection(
                array_map(fn ($set) => new EnrichedSet($set, null), $externalSets->toArray())
            );
        }

        // enrich with available user data

        $setsIds = array_map(fn (UserSet $userSet) => $userSet->getSetId(), $userSets);
        /** @var Set[] $sets */
        $sets = $this->setRepository->findByIdsAndExternalIds($setsIds, array_values(array_map(fn ($set) => $set->getExternalId(), $externalSets->toArray())))->toArray();

        if (!count($sets)) {
            return new EnrichedSetCollection(
                array_map(fn ($set) => new EnrichedSet($set, null), $externalSets->toArray())
            );
        }

        $setsById = [];
        foreach ($sets as $set) {
            $setsById[$set->getId()->value()] = $set;
        }

        $userSetsBySetExternalId = [];
        // associate $sets with $userSets by set externalId
        foreach ($userSets as $userSet) {
            $set = $setsById[$userSet->getSetId()->value()];
            $userSetsBySetExternalId[$set->getExternalId()] = $userSet;
        }

        // merge external sets and user sets in a EnrichedSetCollection
        return new EnrichedSetCollection(
            array_map(
                fn (ExternalSet $set) => new EnrichedSet(
                    $set,
                    $userSetsBySetExternalId[$set->getExternalId()] ?? null
                ),
                $externalSets->toArray()
            )
        );
    }

}
