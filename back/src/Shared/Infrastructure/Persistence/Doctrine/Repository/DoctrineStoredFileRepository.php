<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Repository;

use App\Auth\Infrastructure\Persistence\Doctrine\Entity\DoctrineIdentity;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Repository\StoredFileRepository;
use App\Shared\Infrastructure\Persistence\Doctrine\Entity\DoctrineStoredFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<DoctrineStoredFile>
 */
class DoctrineStoredFileRepository extends ServiceEntityRepository implements StoredFileRepository
{
    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineStoredFile::class);
    }

    public function findById(EntityId $id): ?StoredFile
    {
        $storedFile = parent::findOneBy(['id' => $id->__toString()]);
        return $storedFile?->toDomain();
    }

    public function save(StoredFile $storedFile): void
    {
        $doctrineStoredFile = $this->find($storedFile->getId()) ?? new DoctrineStoredFile();
        $doctrineStoredFile->fromDomain($storedFile);
        $this->entityManager->persist($doctrineStoredFile);
    }

    public function delete(StoredFile $storedFile): void
    {
        $this->entityManager->remove($storedFile);
    }
}
