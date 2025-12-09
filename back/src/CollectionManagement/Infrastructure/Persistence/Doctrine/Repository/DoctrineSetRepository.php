<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Domain\Port\Driven\SetRepository;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSet;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Infrastructure\Persistence\Doctrine\Repository\ExtendedServiceEntityRepository;
use App\Shared\Infrastructure\Persistence\Doctrine\Traits\DoctrineMapToDomainTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ExtendedServiceEntityRepository<DoctrineSet, Set>
 *
 */
#[Autoconfigure]
class DoctrineSetRepository extends ExtendedServiceEntityRepository implements SetRepository
{
    use DoctrineMapToDomainTrait;

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineSet::class, $entityManager);
    }

    #[Override]
    public function save(Set $localSet): void
    {
        parent::attachAndSave($localSet);
    }

    public function findByExternalId(string $externalId): ?Set
    {
        $set = parent::findOneBy(['externalId' => $externalId]);
        return $set?->toDomain();
    }

    public function findById(EntityId $id): ?Set
    {
        $set = parent::find($id->__toString());
        return $set?->toDomain();
    }

    public function findByIdsAndExternalIds(array $setIds, array $externalIds): SetCollection
    {
        return new SetCollection(
            $this->mapToDomain(
                parent::findBy(
                    ['id' => array_map(fn (EntityId $id) => $id->value(), $setIds)],
                    ['externalId' => $externalIds]
            ))
        );
    }
}
