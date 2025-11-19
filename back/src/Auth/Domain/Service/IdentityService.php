<?php

namespace App\Auth\Domain\Service;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\EntityId;

interface IdentityService
{
    public function createIdentity(string $email, string $username, string $password): ?Identity;

    public function getIdentityById(EntityId $id): ?Identity;

    public function getIdentityByIdentifier(string $identifier): ?Identity;
}
