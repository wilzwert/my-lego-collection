<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Port\Driven\ElementRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineElement;
use App\Shared\Domain\Model\EntityId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DoctrineElement>
 * @author Wilhelm Zwertvaegher
 */
class DoctrineElementRepository extends ServiceEntityRepository implements ElementRepository
{

    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineElement::class);
    }

    public function findById(EntityId $id): ?Element
    {
        $result = parent::find($id);
        return $result?->toDomain();
    }

    public function findByExternalIds(array $externalIds): array
    {
        return array_map(
            fn (DoctrineElement $element) => $element->toDomain(),
            parent::findBy(['externalId' => $externalIds])
        );
    }

    public function save(Element $element): void
    {
        $doctrineElement = $this->find($element->getId()) ?? new DoctrineElement();
        $doctrineElement->fromDomain($element);
        $this->entityManager->persist($doctrineElement);
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
}
