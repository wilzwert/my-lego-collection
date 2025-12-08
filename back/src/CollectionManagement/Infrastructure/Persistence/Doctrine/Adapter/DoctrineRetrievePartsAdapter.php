<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Adapter;

use App\CollectionManagement\Domain\Model\Local\Part;
use App\CollectionManagement\Domain\Port\Driven\PartRepository;
use App\CollectionManagement\Domain\Port\Driven\RetrieveParts;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DoctrineRetrievePartsAdapter implements RetrieveParts
{

    public function __construct(private PartRepository $repository)
    {

    }

    /**
     * @param array<string> $externalIds
     * @return array<Part>
     */
    public function byExternalIds(array $externalIds): array
    {
        return $this->repository->findByExternalIds($externalIds);
    }
}
