<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\UserSetCollection;
use App\CollectionManagement\Domain\Repository\UserSetRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineUserSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure]
class DoctrineUserSetRepository extends ServiceEntityRepository implements UserSetRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Set::class);
    }

    public function findByUserAndExternalIds(string $userId, array $externalIds): UserSetCollection
    {
        return new UserSetCollection(
            array_map(
                fn(DoctrineUserSet $doctrineUserSet): UserSet => $doctrineUserSet->toDomain(),
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
}
