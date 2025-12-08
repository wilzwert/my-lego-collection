<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Part;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Domain\Port\Driven\SetElementRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrinePart;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSet;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSetElement;
use App\Shared\Domain\Model\EntityId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<DoctrineSetElement>
 *
 */
#[Autoconfigure]
class DoctrineSetElementRepository extends ServiceEntityRepository implements SetElementRepository
{
    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineSetElement::class);
    }

    #[Override]
    public function save(SetElement $setElement): void
    {
        $doctrineSetElement = $this->find($setElement->getId()) ?? new DoctrineSetElement();
        $doctrineSetElement->fromDomain($setElement);
        $this->entityManager->persist($doctrineSetElement);
    }
    public function saveAll(array $setElements): void
    {
        $ids = array_map(fn (SetElement $part) => $part->getId(), $setElements);

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
                $entity = new DoctrinePart();
                $entity->fromDomain($setElement);
                $this->entityManager->persist($entity);
            }
        }
    }
}
