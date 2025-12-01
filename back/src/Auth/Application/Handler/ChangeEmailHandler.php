<?php

namespace App\Auth\Application\Handler;

use App\Auth\Application\Command\ChangeEmailCommand;
use App\Auth\Application\Command\GetIdentityQuery;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;

readonly class ChangeEmailHandler
{
    public function __construct(
        private IdentityService $identityService,
        private EventBus $eventBus

    ) {
    }

    public function __invoke(ChangeEmailCommand $command): ?Identity
    {
        $identity = $this->identityService->changeEmail(EntityId::fromString($command->identityId), $command->email);
        $this->eventBus->dispatchAll($identity);
        return $identity;
    }
}
