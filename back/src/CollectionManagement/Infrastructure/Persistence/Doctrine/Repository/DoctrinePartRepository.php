<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Part;
use App\CollectionManagement\Domain\Port\Driven\PartRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrinePart;
use App\Shared\Domain\Model\EntityId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<DoctrinePart>
 */
class DoctrinePartRepository extends ServiceEntityRepository implements PartRepository
{

    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrinePart::class);
    }

    public function findById(EntityId $id): ?Part
    {
        $found =  parent::find($id->__toString());
        return $found?->toDomain();
    }

    public function findByExternalIds(array $externalIds): array
    {
        return array_map(
            fn (DoctrinePart $part) => $part->toDomain(),
            parent::findBy(['externalId' => $externalIds])
        );
    }

    public function save(Part $part): void
    {
        $doctrinePart = $this->find($part->getId()) ?? new DoctrinePart();
        $doctrinePart->fromDomain($part);
        $this->entityManager->persist($doctrinePart);
    }

    public function saveAll(array $parts): void
    {
        $ids = array_map(fn (Part $part) => $part->getId(), $parts);

        $qb = $this->createQueryBuilder('p');
        $existingIds = $qb->select('p.id')
            ->where($qb->expr()->in('p.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getScalarResult();
        $existingIds = array_map(fn ($r) => $r['id'], $existingIds);
        $existingMap = array_fill_keys($existingIds, true);

        foreach ($parts as $part) {
            $id = $part->getId()->__toString();
            if (isset($existingMap[$id])) {
                $managed = $this->entityManager->getReference(DoctrinePart::class, $id);
                $managed->fromDomain($part);
            } else {
                $entity = new DoctrinePart();
                $entity->fromDomain($part);
                $this->entityManager->persist($entity);
            }
        }
    }
}
