<?php

namespace App\Auth\Application\Handler;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Domain\Service\IdentityService;

readonly class RegistrationHandler
{
    public function __construct(
        private readonly IdentityService $identityService
    ){
    }

    public function __invoke(RegistrationCommand $command): void
    {
        $this->identityService->createIdentity($command);
    }
}
