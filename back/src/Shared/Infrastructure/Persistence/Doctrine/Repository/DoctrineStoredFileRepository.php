<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Repository;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
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
 * @extends ServiceEntityRepository<DoctrineIdentity>
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
        $this->entityManager->persist(
            new DoctrineStoredFile(
                $storedFile->getId(),
                $storedFile->getPath(),
                $storedFile->getFilename(),
                $storedFile->getMimeType(),
                $storedFile->getExtension(),
                $storedFile->getType(),
                $storedFile->getCreatedAt()
            )
        );
    }

    public function delete(StoredFile $storedFile): void
    {
        $this->entityManager->remove($storedFile);
    }
}
