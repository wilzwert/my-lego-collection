<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Domain\Port\Driven\LocalSetRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Override;
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

    #[Override]
    public function save(Set $localSet): void
    {
        $doctrineSet = $this->find($localSet->getId()) ?? new DoctrineSet();
        $doctrineSet->fromDomain($localSet);
        $this->entityManager->persist($doctrineSet);
    }

    #[Override]
    public function findByUserAndExternalIds(string $userId, array $externalIds): SetCollection
    {
        return new SetCollection(
            array_map(
                fn (DoctrineSet $s) => $s->toDomain(),
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

    public function findByExternalId(string $externalId): ?Set
    {
        $set = parent::find(['externalId' => $externalId]);
        return $set?->toDomain();
    }
}
