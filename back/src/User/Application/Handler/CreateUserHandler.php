<?php

namespace App\User\Application\Handler;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\User\Domain\Service\UserService;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;

readonly class CreateUserHandler
{
    public function __construct(
        private UserService $userService,
        private EventBus    $eventBus
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $user = $this->userService->createUser(EntityId::fromString($command->getId()));
        $this->eventBus->dispatchAll($user);
    }
}
