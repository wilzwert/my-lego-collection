<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Repository;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Infrastructure\Persistence\Doctrine\Entity\DoctrineStoredFile;
use App\Shared\Infrastructure\Persistence\Doctrine\Repository\ExtendedServiceEntityRepository;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\UserRepository;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ExtendedServiceEntityRepository<DoctrineUser, User>
 */
class DoctrineUserRepository extends ExtendedServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineUser::class, $entityManager);
    }

    public function findById(EntityId $userId): ?User
    {
        $user = $this->find($userId->__toString());
        return $user?->toDomain();
    }

    public function save(User $user): void
    {
        $doctrineUser = $this->find($user->getId()) ?? new DoctrineUser();
        $doctrineUser->fromDomain($user, $user->getAvatar() ? $this->entityManager->find(DoctrineStoredFile::class, $user->getAvatar()->getId()) : null);
        $this->entityManager->persist($doctrineUser);
    }

    public function findByIdentityId(EntityId $identityId): ?User
    {
        $user = parent::findOneBy(['identityId' => (string)$identityId]);
        return $user?->toDomain();
    }
}
