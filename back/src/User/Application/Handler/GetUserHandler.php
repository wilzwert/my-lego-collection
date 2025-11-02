<?php

namespace App\User\Application\Handler;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\User\Application\Command\GetUserQuery;

readonly class GetUserHandler
{
    public function __construct(
        private IdentityService $userService
    )
    {}

    public function __invoke(GetUserQuery $query): ?Identity
    {
        return $this->userService->getIdentityByIdentifier($query->getIdentifier());
    }
}
