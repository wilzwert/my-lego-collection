<?php

namespace App\Auth\Infrastructure\Persistence\Doctrine\Repository;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Infrastructure\Persistence\Doctrine\Entity\DoctrineIdentity;
use App\Shared\Domain\Model\EntityId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<DoctrineIdentity>
 */
class DoctrineIdentityRepository extends ServiceEntityRepository implements IdentityRepository
{
    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineIdentity::class);
    }

    public function findByEmail(string $email): ?Identity
    {
        $doctrineIdentity = parent::findOneBy(['email' => $email]);
        if (!$doctrineIdentity) {
            return null;
        }
        return $doctrineIdentity->toDomain();
    }

    public function findByUsername(string $username): ?Identity
    {
        $doctrineIdentity = parent::findOneBy(['username' => $username]);
        if (!$doctrineIdentity) {
            return null;
        }
        return $doctrineIdentity->toDomain();
    }

    public function findByEmailOrUsername(string $email, string $username): ?Identity
    {
        $doctrineIdentity = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere('u.username = :username')
            ->setParameter('email', $email)
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();

        return $doctrineIdentity ? $doctrineIdentity->toDomain() : null;
    }

    public function findByIdentifier(string $identifier): ?Identity
    {
        if (!str_contains($identifier, '@')) {
            return $this->findByUsername($identifier);
        }
        return $this->findByEmail($identifier);
    }

    public function findById(EntityId $id): ?Identity
    {
        $user = parent::findOneBy(['id' => $id->__toString()]);
        return $user?->toDomain();
    }

    public function save(Identity $identity): void
    {
        $this->entityManager->persist(
            new DoctrineIdentity(
                $identity->getId(),
                $identity->getEmail(),
                $identity->getUsername(),
                $identity->getPasswordHash(),
                $identity->getRoles()
            )
        );
    }
}
