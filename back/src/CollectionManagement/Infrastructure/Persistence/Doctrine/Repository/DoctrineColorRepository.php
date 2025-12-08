<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Color;
use App\CollectionManagement\Domain\Model\Local\Part;
use App\CollectionManagement\Domain\Port\Driven\ColorRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineColor;
use App\Shared\Domain\Model\EntityId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<DoctrineColor>
 */
class DoctrineColorRepository extends ServiceEntityRepository implements ColorRepository
{

    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineColor::class);
    }

    public function findById(EntityId $id): ?Color
    {
        $found =  parent::find($id->__toString());
        return $found?->toDomain();
    }

    public function findByExternalIds(array $externalIds): array
    {
        return array_map(
            fn (DoctrineColor $color) => $color->toDomain(),
            parent::findBy(['externalId' => $externalIds])
        );
    }

    public function save(Color $color): void
    {
        $doctrineColor = $this->find($color->getId()) ?? new DoctrineColor();
        $doctrineColor->fromDomain($color);
        $this->entityManager->persist($doctrineColor);
    }

    public function saveAll(array $colors): void
    {
        $ids = array_map(fn (Color $color) => $color->getId(), $colors);

        $qb = $this->createQueryBuilder('c');
        $existingIds = $qb->select('c.id')
            ->where($qb->expr()->in('j.id', ':ids'))
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
