<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Color;
use App\CollectionManagement\Domain\Port\Driven\ColorRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineColor;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Infrastructure\Persistence\Doctrine\Repository\ExtendedServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ExtendedServiceEntityRepository<DoctrineColor, Color>
 */
class DoctrineColorRepository extends ExtendedServiceEntityRepository implements ColorRepository
{

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineColor::class, $entityManager);
    }

    public function findById(EntityId $id): ?Color
    {
        $found =  parent::find($id->__toString());
        return $found?->toDomain();
    }

    public function findByExternalIds(array $externalIds): array
    {
        return $this->mapToDomain(parent::findBy(['externalId' => $externalIds]));
    }

    public function save(Color $color): void
    {
        parent::attachAndSave($color);
    }

    public function saveAll(array $colors): void
    {
        $ids = array_map(fn (Color $color) => $color->getId(), $colors);

        $qb = $this->createQueryBuilder('c');
        $existingIds = $qb->select('c.id')
            ->where($qb->expr()->in('c.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getScalarResult();
        $existingIds = array_map(fn ($r) => $r['id'], $existingIds);
        $existingMap = array_fill_keys($existingIds, true);

        foreach ($colors as $color) {
            $id = $color->getId()->__toString();
            if (isset($existingMap[$id])) {
                $managed = $this->entityManager->getReference(DoctrineColor::class, $id);
                $managed->fromDomain($color);
            } else {
                $entity = new DoctrineColor();
                $entity->fromDomain($color);
                $this->entityManager->persist($entity);
            }
        }
    }
}
