<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Repository;

use App\Auth\Infrastructure\Persistence\Doctrine\Entity\DoctrineIdentity;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Infrastructure\Persistence\Doctrine\Entity\DoctrineStoredFile;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<DoctrineIdentity>
 */
class DoctrineUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineUser::class);
    }

    public function findById(EntityId $userId): ?User
    {
        $user = parent::find($userId->__toString());
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
