<?php

namespace App\User\Application\Handler;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Model\Uuid;
use App\User\Application\Command\GetUserByIdentityQuery;
use App\User\Domain\Model\User;
use App\User\Domain\Service\UserService;

readonly class GetUserHandler
{
    public function __construct(
        private UserService $userService
    )
    {}

    public function __invoke(GetUserByIdentityQuery $query): ?User
    {
        return $this->userService->getUserByIdentityId(Uuid::fromString($query->identityId));
    }
}
