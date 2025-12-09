<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\UserSetElement;
use App\CollectionManagement\Domain\Port\Driven\UserSetElementRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineUserSetElement;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Infrastructure\Persistence\Doctrine\Repository\ExtendedServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ExtendedServiceEntityRepository<DoctrineUserSetElement, UserSetElement>
 *
 */
#[Autoconfigure]
class DoctrineUserSetElementRepository extends ExtendedServiceEntityRepository implements UserSetElementRepository
{
    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineUserSetElement::class, $entityManager);
    }

    public function findById(EntityId $id): ?UserSetElement
    {
        return parent::find($id)?->toDomain();
    }

    public function save(UserSetElement $userSetElement): void
    {
        parent::attachAndSave($userSetElement);
    }

    public function saveAll(array $userSetElements): void
    {
        parent::attachAndSaveAll($userSetElements);
    }

    /**
     * @param EntityId $setId
     * @return array<UserSetElement>
     */
    public function findByUserSetId(EntityId $setId): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where($qb->expr()->eq('u.userSetId', ':id'))
            ->setParameter('id', $setId->__toString());

        return $this->mapToDomain($qb->getQuery()->getResult());
    }
}
