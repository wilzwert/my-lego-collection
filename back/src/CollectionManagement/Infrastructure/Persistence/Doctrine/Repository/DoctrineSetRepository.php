<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\LocalSet;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Domain\Repository\LocalSetRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure]
class DoctrineSetRepository extends ServiceEntityRepository implements LocalSetRepository
{
    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, LocalSet::class);
    }

    #[\Override]
    public function add(LocalSet $localSet): void
    {
        $this->entityManager->persist($localSet);
    }

    public function update(LocalSet $localSet): void
    {
        // Nothing to do as we use Doctrine, and all changes to the entity are implicitly handled by doctrine
        // as long as the LocalSet is handled by doctrine itself which MUST be the case here
    }

    public function findByUserAndExternalIds(string $userId, array $externalIds): SetCollection
    {
        return new SetCollection($this->createQueryBuilder('s')
            ->join('s.userSets', 'us')
            ->where('us.user = :userId')
            ->andWhere('s.externalId IN (:externalIds)')
            ->setParameter('userId', $userId)
            ->setParameter('externalIds', $externalIds)
            ->getQuery()
            ->getResult()
        );
    }
}
