<?php

namespace App\User\Application\Handler;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Service\UserService;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;

readonly class IdentityCreatedHandler
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    public function __invoke(IdentityCreatedIntegrationEvent $event): void
    {
        if ($event->type() != 'auth.identity.created') {
            throw new \InvalidArgumentException('Event type should be auth.identity.created');
        }
        $this->userService->createUser(EntityId::fromString($event->getId()));
    }
}
