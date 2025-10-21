<?php

namespace App\User\Application\Handler;

use App\User\Application\Command\RegisterUserCommand;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\UserService;

class RegisterUserHandler
{
    public function __construct(
        private readonly UserService $userService
    )
    {}

    public function __invoke(RegisterUserCommand $command): void
    {
        $this->userService->createUser($command);
    }
}
