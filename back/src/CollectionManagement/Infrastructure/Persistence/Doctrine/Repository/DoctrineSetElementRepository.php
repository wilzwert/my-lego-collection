<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Port\Driven\SetElementRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSetElement;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Infrastructure\Persistence\Doctrine\Repository\ExtendedServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ExtendedServiceEntityRepository<DoctrineSetElement, SetElement>
 *
 */
#[Autoconfigure]
class DoctrineSetElementRepository extends ExtendedServiceEntityRepository implements SetElementRepository
{

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineSetElement::class, $entityManager);
    }

    #[Override]
    public function save(SetElement $setElement): void
    {
        parent::attachAndSave($setElement);
    }
    public function saveAll(array $setElements): void
    {
        $ids = array_map(fn (SetElement $setElement) => $setElement->getId(), $setElements);

        $qb = $this->createQueryBuilder('s');
        $existingIds = $qb->select('s.id')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getScalarResult();
        $existingIds = array_map(fn ($r) => $r['id'], $existingIds);
        $existingMap = array_fill_keys($existingIds, true);

        foreach ($setElements as $setElement) {
            $id = $setElement->getId()->__toString();
            if (isset($existingMap[$id])) {
                $managed = $this->entityManager->getReference(DoctrineSetElement::class, $id);
                $managed->fromDomain($setElement);
            } else {
                $entity = new DoctrineSetElement();
                $entity->fromDomain($setElement);
                $this->entityManager->persist($entity);
            }
        }
    }

    public function findBySetId(EntityId $setId): array
    {
        return $this->mapToDomain(parent::findBy(['setId' => $setId]));
    }
}
