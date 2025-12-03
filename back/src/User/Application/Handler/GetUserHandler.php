<?php

namespace App\User\Application\Handler;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Model\EntityId;
use App\User\Application\Command\GetUserByIdentityQuery;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\UserRepository;
use App\User\Domain\Service\UserService;

readonly class GetUserHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(GetUserByIdentityQuery $query): ?User
    {
        return $this->userRepository->findByIdentityId(EntityId::fromString($query->identityId));
    }
}
