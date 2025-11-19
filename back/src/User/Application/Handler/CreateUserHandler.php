<?php

namespace App\User\Application\Handler;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Domain\Service\IdentityService;
use App\User\Application\Command\CreateUserCommand;
use App\User\Domain\Service\UserService;

readonly class CreateUserHandler
{
    public function __construct(
        private readonly UserService $userService,
    ){
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $this->userService->createUser($command);
    }
}
