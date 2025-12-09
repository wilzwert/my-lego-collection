<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Repository;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Port\Driven\StoredFileRepository;
use App\Shared\Infrastructure\Persistence\Doctrine\Entity\DoctrineStoredFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ExtendedServiceEntityRepository<DoctrineStoredFile, StoredFile>
 */
class DoctrineStoredFileRepository extends ExtendedServiceEntityRepository implements StoredFileRepository
{
    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineStoredFile::class, $entityManager);
    }

    public function findById(EntityId $id): ?StoredFile
    {
        $storedFile = parent::findOneBy(['id' => $id->__toString()]);
        return $storedFile?->toDomain();
    }

    public function save(StoredFile $storedFile): void
    {
        parent::attachAndSave($storedFile);
    }

    public function delete(StoredFile $storedFile): void
    {
        $this->entityManager->remove($storedFile);
    }
}
