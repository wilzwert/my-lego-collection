<?php

namespace App\Auth\Application\Handler;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Service\EventBus;

readonly class RegistrationHandler
{
    public function __construct(
        private IdentityService $identityService,
        private EventBus        $eventBus
    ) {
    }

    public function __invoke(RegistrationCommand $command): void
    {
        $identity = $this->identityService->createIdentity($command->email, $command->username, $command->password);
        $this->eventBus->dispatchAll($identity);
    }
}
