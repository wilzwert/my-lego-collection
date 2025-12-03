<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Adapter;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\RetrieveUserForIdentity;
use App\User\Domain\Port\Driven\UserRepository;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DoctrineRetrieveUserForIdentityAdapter implements RetrieveUserForIdentity
{

    public function __construct(private UserRepository $userRepository)
    {
    }

    public function retrieveUser(EntityId $identityId): ?User
    {
        return $this->userRepository->findByIdentityId($identityId);
    }
}
