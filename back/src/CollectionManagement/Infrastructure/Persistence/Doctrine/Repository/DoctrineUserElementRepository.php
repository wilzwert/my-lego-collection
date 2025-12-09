<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\UserElement;
use App\CollectionManagement\Domain\Port\Driven\UserElementRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineUserElement;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Infrastructure\Persistence\Doctrine\Repository\ExtendedServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ExtendedServiceEntityRepository<DoctrineUserElement, UserElement>
 * @author Wilhelm Zwertvaegher
 */
class DoctrineUserElementRepository extends ExtendedServiceEntityRepository implements UserElementRepository
{

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, DoctrineUserElement::class, $entityManager);
    }

    public function save(UserElement $setElement): void
    {
        parent::attachAndSave($setElement);
    }

    public function saveAll(array $userElements): void
    {
        parent::attachAndSaveAll($userElements);
    }

    /**
     * @param EntityId $userId
     * @return list<UserElement>
     */
    public function findByUserId(EntityId $userId): array
    {
        return $this->mapToDomain(
            parent::findBy(['userId' => $userId])
        );
    }

    /**
     * @param array $elementsIds
     * @return array
     */
    public function findByElementsIds(array $elementsIds): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('u')
            ->from(DoctrineUserElement::class, 'u')
            ->where($qb->expr()->in('u.elementId', ':ids'))
            ->setParameter('ids', $elementsIds);

        return $this->mapToDomain($qb->getQuery()->getResult());
    }
}
