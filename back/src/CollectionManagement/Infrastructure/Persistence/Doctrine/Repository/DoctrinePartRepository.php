<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;
use App\CollectionManagement\Domain\Model\Part;
use App\CollectionManagement\Domain\Repository\PartRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrinePartRepository extends ServiceEntityRepository implements PartRepository  {
    public function __construct(ManagerRegistry $entityManager) {
        parent::__construct($entityManager, Part::class);
    }

}

