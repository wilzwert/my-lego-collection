<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetStatus;
use App\CollectionManagement\Domain\Model\UserSetCollection;
use App\CollectionManagement\Domain\Port\Driven\UserSetRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSet;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineUserSet;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Infrastructure\Persistence\Doctrine\Repository\ExtendedServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @author W.Zwertvaegher
 * @extends ExtendedServiceEntityRepository<DoctrineUserSet, UserSet>
 */
#[Autoconfigure]
class DoctrineUserSetRepository extends ExtendedServiceEntityRepository implements UserSetRepository
{
    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineUserSet::class, $entityManager);
    }

    /**
     * @param EntityId $id
     * @return UserSet|null
     */
    #[\Override]
    public function findById(EntityId $id): ?UserSet
    {
        return parent::find($id->value())?->toDomain();
    }

    #[\Override]
    public function findByUserId(EntityId $userId): UserSetCollection
    {
        return new UserSetCollection(
            array_map(
                fn (DoctrineUserSet $doctrineUserSet): UserSet => $doctrineUserSet->toDomain(),
                parent::findBy(['userId' => $userId->value()])
            )
        );
    }

    #[\Override]
    public function findIncompleteOwnedBySetId(EntityId $setId): UserSetCollection
    {
        return new UserSetCollection(
            array_map(
                fn (DoctrineUserSet $doctrineUserSet): UserSet => $doctrineUserSet->toDomain(),
                parent::findBy([
                    'status' => [UserSetStatus::OWNED, UserSetStatus::BUILT],
                    'creationStatus' => UserSetCreationStatus::CREATED,
                    'setId' => $setId
                ])
            )
        );
    }

    #[\Override]
    public function save(UserSet $userSet): void
    {
        parent::attachAndSave($userSet);
    }
}
