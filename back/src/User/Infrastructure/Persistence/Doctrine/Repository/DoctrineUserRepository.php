<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Repository;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\Shared\Domain\Uuid;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineUser::class);
    }

    public function findByEmail(string $email): ?User
    {
        $doctrineUser = parent::findByEmail($email);
        if(!$doctrineUser){
            return null;
        }
        return $doctrineUser->toDomain();
    }

    public function findByEmailOrUsername(string $email, string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere('u.username = :username')
            ->setParameter('email', $email)
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByIdentifier(string $identifier): ?User
    {
        if(false === strpos($identifier, '@')){
            return $this->findByUsername($identifier);
        }
        return $this->findByEmail($identifier);
    }

    public function findById(Uuid $uuid): ?User
    {
        return parent::findById($uuid->__toString());
    }

    public function save(User $user): void
    {
        $this->entityManager->persist(new DoctrineUser($user));
    }
}
