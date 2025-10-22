<?php

namespace App\User\Domain\Service;

use App\User\Application\Command\RegisterUserCommand;
use App\User\Domain\Entity\User;

interface UserService
{
    public function createUser(RegisterUserCommand $command): ?User;

    public function getUserByIdentifier(string $identifier): ?User;
}
