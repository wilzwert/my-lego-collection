<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Domain\Repository\LocalSetRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<DoctrineSet>
 *
 */
#[Autoconfigure]
class DoctrineSetRepository extends ServiceEntityRepository implements LocalSetRepository
{
    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineSet::class);
    }

    #[\Override]
    public function add(Set $localSet): void
    {
        $this->entityManager->persist($localSet);
    }

    #[\Override]
    public function update(Set $localSet): void
    {
        // Nothing to do as we use Doctrine, and all changes to the entity are implicitly handled by doctrine
        // as long as the Set is handled by doctrine itself which MUST be the case here
    }

    #[\Override]
    public function findByUserAndExternalIds(string $userId, array $externalIds): SetCollection
    {
        return new SetCollection(
            array_map(
                fn(DoctrineSet $s) => $s->toDomain(),
                $this->createQueryBuilder('s')
                    ->join('s.userSets', 'us')
                    ->where('us.user = :userId')
                    ->andWhere('s.externalId IN (:externalIds)')
                    ->setParameter('userId', $userId)
                    ->setParameter('externalIds', $externalIds)
                    ->getQuery()
                    ->getResult()
            )
        );
    }
}
