<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Port\Driven\ElementRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineElement;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Infrastructure\Persistence\Doctrine\Repository\ExtendedServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ExtendedServiceEntityRepository<DoctrineElement, Element>
 * @author Wilhelm Zwertvaegher
 */
class DoctrineElementRepository extends ExtendedServiceEntityRepository implements ElementRepository
{

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineElement::class, $entityManager);
    }

    public function findById(EntityId $id): ?Element
    {
        $result = parent::find($id);
        return $result?->toDomain();
    }

    public function findByExternalIds(array $externalIds): array
    {
        return $this->mapToDomain(parent::findBy(['externalId' => $externalIds]));
    }

    public function save(Element $element): void
    {
        parent::attachAndSave($element);
    }

    public function saveAll(array $elements): void
    {
        $ids = array_map(fn (Element $element) => $element->getId(), $elements);

        $qb = $this->createQueryBuilder('e');
        $existingIds = $qb->select('e.id')
            ->where($qb->expr()->in('e.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getScalarResult();
        $existingIds = array_map(fn ($r) => $r['id'], $existingIds);
        $existingMap = array_fill_keys($existingIds, true);

        foreach ($elements as $element) {
            $id = $element->getId()->__toString();
            if (isset($existingMap[$id])) {
                $managed = $this->entityManager->getReference(DoctrineElement::class, $id);
                $managed->fromDomain($element);
            } else {
                $entity = new DoctrineElement();
                $entity->fromDomain($element);
                $this->entityManager->persist($entity);
            }
        }
    }

    public function findByIds(array $ids): array
    {
        return $this->mapToDomain(parent::findBy(['id' => $ids]));
    }
}
