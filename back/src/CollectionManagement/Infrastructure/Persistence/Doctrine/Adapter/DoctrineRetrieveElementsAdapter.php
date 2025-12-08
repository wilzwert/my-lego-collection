<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Adapter;

use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Port\Driven\ElementRepository;
use App\CollectionManagement\Domain\Port\Driven\RetrieveElements;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DoctrineRetrieveElementsAdapter implements RetrieveElements
{

    public function __construct(private ElementRepository $repository)
    {

    }

    /**
     * @param array<string> $externalIds
     * @return array<Element>
     */
    public function byExternalIds(array $externalIds): array
    {
        return $this->repository->findByExternalIds($externalIds);
    }
}
