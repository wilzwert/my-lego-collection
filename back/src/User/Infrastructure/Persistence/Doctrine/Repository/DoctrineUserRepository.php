<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Repository;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Infrastructure\Persistence\Doctrine\Entity\DoctrineIdentity;
use App\Shared\Domain\Model\Uuid;
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

    public function findById(Uuid $uuid): ?User
    {
        $user = parent::findOneBy(['id' => $uuid->__toString()]);
        return $user?->toDomain();
    }

    public function save(User $user): void
    {
        $this->entityManager->persist(
            new DoctrineUser(
                $user->getId(),
                $user->getIdentityId(),
                $user->getCreatedAt(),
                $user->getUpdatedAt()
            )
        );
    }

    public function findByIdentityId(Uuid $identityId): ?User
    {
        // TODO: Implement findByIdentityId() method.
        $user = parent::findOneBy(['identityId' => (string)$identityId]);
        return $user?->toDomain();
    }
}
