<?php

namespace App\User\Application\Handler;

use App\User\Application\Command\GetUserQuery;
use App\User\Application\Command\RegisterUserCommand;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\UserService;

class GetUserHandler
{
    public function __construct(
        private readonly UserService $userService
    )
    {}

    public function __invoke(GetUserQuery $query): ?User
    {
        return $this->userService->getUserByIdentifier($query->getIdentifier());
    }
}
