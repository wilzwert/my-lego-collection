<?php

namespace App\CollectionManagement\Domain\Service;


use App\CollectionManagement\Domain\Model\EnrichedSet;
use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Domain\Repository\LocalSetRepository;
use App\CollectionManagement\Domain\Repository\UserSetRepository;
use App\Shared\Domain\Uuid;

class DefaultSetService implements SetService
{

    public function __construct(
        private readonly LegoDataProvider $legoDataProvider,
        private readonly UserSetRepository $userSetRepository,
    )
    {}

    /**
     * @inheritDoc
     */
    public function findSets(string $search, ?Uuid $userId = null): EnrichedSetCollection
    {
        // get sets from data provider
        $externalSets = $this->legoDataProvider->findSets($search);

        if($userId === null) {
            return new EnrichedSetCollection(
                array_map(fn($set) => new EnrichedSet($set, null), $externalSets->toArray())
            );
        }

        // enrich with user data
        // get user UserSet as an array
        $userSets = $this->userSetRepository->findByUserAndExternalIds(
            $userId,
            array_flip(array_map(fn($set) => $set->getExternalId(), $externalSets->toArray())),
        )->toArray();

        // merge external sets and user sets in a EnrichedSetCollection
        return new EnrichedSetCollection(
            array_map(
                fn($set) => new EnrichedSet(
                    $set,
                    array_find($userSets, fn($s) => $s->getExternalId() === $set->getExternalId())
                ),
                $externalSets->toArray()
            )
        );
    }
}
