<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Repository;

use App\Shared\Domain\Uuid;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<DoctrineUser>
 */
class DoctrineUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineUser::class);
    }

    public function findByEmail(string $email): ?User
    {
        $doctrineUser = parent::findOneBy(['email' => $email]);
        if (!$doctrineUser) {
            return null;
        }
        return $doctrineUser->toDomain();
    }

    public function findByUsername(string $username): ?User
    {
        $doctrineUser = parent::findOneBy(['username' => $username]);
        if (!$doctrineUser) {
            return null;
        }
        return $doctrineUser->toDomain();
    }

    public function findByEmailOrUsername(string $email, string $username): ?User
    {
        $doctrineUser = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere('u.username = :username')
            ->setParameter('email', $email)
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();

        return $doctrineUser ? $doctrineUser->toDomain() : null;
    }

    public function findByIdentifier(string $identifier): ?User
    {
        if (!str_contains($identifier, '@')) {
            return $this->findByUsername($identifier);
        }
        return $this->findByEmail($identifier);
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
                $user->getEmail(),
                $user->getUsername(),
                $user->getPasswordHash(),
                $user->getRoles()
            )
        );
    }
}
