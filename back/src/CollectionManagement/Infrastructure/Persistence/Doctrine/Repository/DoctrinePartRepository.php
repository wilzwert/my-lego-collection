<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Part;
use App\CollectionManagement\Domain\Port\Driven\PartRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<Part>
 */
class DoctrinePartRepository extends ServiceEntityRepository implements PartRepository
{
    public function __construct(ManagerRegistry $entityManager)
    {
        parent::__construct($entityManager, Part::class);
    }
}
