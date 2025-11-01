<?php

namespace App\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\EnrichedSet;
use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Repository\UserSetRepository;
use App\Shared\Domain\Uuid;
use Override;

readonly class DefaultSetService implements SetService
{

    public function __construct(
        private LegoDataProvider $legoDataProvider,
        private UserSetRepository $userSetRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findSets(string $search, ?Uuid $userId = null): EnrichedSetCollection
    {
        // get sets from external data provider
        $externalSets = $this->legoDataProvider->findSets($search);
        // if current user is not set, then we can return found data as is
        if ($userId === null) {
            return new EnrichedSetCollection(
                array_map(fn ($set) => new EnrichedSet($set, null), $externalSets->toArray())
            );
        }

        // enrich with available user data
        // get user's UserSets as an array
        $userSets = $this->userSetRepository->findByUserAndExternalIds(
            $userId,
            array_values(array_map(fn ($set) => $set->getExternalId(), $externalSets->toArray())),
        )->toArray();

        // merge external sets and user sets in a EnrichedSetCollection
        return new EnrichedSetCollection(
            array_map(
                fn ($set) => new EnrichedSet(
                    $set,
                    array_find($userSets, fn ($s) => $s->getLocalSet()->getExternalId() === $set->getExternalId())
                ),
                $externalSets->toArray()
            )
        );
    }
}
