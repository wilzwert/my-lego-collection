<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Adapter;

use App\CollectionManagement\Domain\Model\Local\Color;
use App\CollectionManagement\Domain\Port\Driven\ColorRepository;
use App\CollectionManagement\Domain\Port\Driven\RetrieveColors;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DoctrineRetrieveColorsAdapter implements RetrieveColors
{

    public function __construct(private ColorRepository $repository)
    {

    }

    /**
     * @param array<string> $externalIds
     * @return array<Color>
     */
    public function byExternalIds(array $externalIds): array
    {
        return $this->repository->findByExternalIds($externalIds);
    }
}
