<?php

namespace App\Auth\Domain\Service;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Domain\Model\Identity;

interface IdentityService
{
    public function createIdentity(RegistrationCommand $command): ?Identity;

    public function getIdentityByIdentifier(string $identifier): ?Identity;
}
