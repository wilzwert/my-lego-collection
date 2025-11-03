<?php

namespace App\Auth\Domain\Service;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\Uuid;

interface IdentityService
{
    public function createIdentity(RegistrationCommand $command): ?Identity;

    public function getIdentityById(Uuid $id): ?Identity;

    public function getIdentityByIdentifier(string $identifier): ?Identity;
}
