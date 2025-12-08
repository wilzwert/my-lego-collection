<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Model\UserSetCollection;
use App\CollectionManagement\Domain\Port\Driven\UserSetRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSet;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineUserSet;
use App\Shared\Domain\Model\EntityId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @author W.Zwertvaegher
 * @extends ServiceEntityRepository<UserSet>
 */
#[Autoconfigure]
class DoctrineUserSetRepository extends ServiceEntityRepository implements UserSetRepository
{
    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineUserSet::class);
    }

    public function findByUserAndExternalIds(EntityId $userId, array $externalIds): UserSetCollection
    {
        return new UserSetCollection(
            array_map(
                fn (DoctrineUserSet $doctrineUserSet): UserSet => $doctrineUserSet->toDomain(),
                $this->createQueryBuilder('us')
                    ->join('us.set', 's')
                    ->where('us.user = :userId')
                    ->andWhere('s.externalId IN (:externalIds)')
                    ->setParameter('userId', $userId)
                    ->setParameter('externalIds', $externalIds)
                    ->getQuery()
                    ->getResult()
            )
        );
    }

    public function findIncompleteBySet(Set $set): UserSetCollection
    {
        return new UserSetCollection(
            array_map(
                fn (DoctrineUserSet $doctrineUserSet): UserSet => $doctrineUserSet->toDomain(),
                parent::findBy(['creationStatus' => UserSetCreationStatus::CREATED, 'set' => $set])
            )
        );
    }

    public function save(UserSet $userSet): void
    {
        $doctrineUserSet = $this->find($userSet->getId()) ?? new DoctrineUserSet();
        $doctrineUserSet->fromDomain($userSet, $this->entityManager->find(DoctrineSet::class, $userSet->getSet()->getId()));
        $this->entityManager->persist($doctrineUserSet);
    }
}
