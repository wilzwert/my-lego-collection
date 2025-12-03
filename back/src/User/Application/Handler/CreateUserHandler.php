<?php

namespace App\User\Application\Handler;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use App\User\Domain\Port\Driven\UserRepository;
use App\User\Domain\Service\UserService;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;

readonly class CreateUserHandler
{
    public function __construct(
        private UserService $userService,
        private UserRepository $userRepository,
        private TransactionProvider $transactionProvider,
        private EventBus    $eventBus
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $this->transactionProvider->transactional(function () use ($command) {
            $user = $this->userService->createUser(EntityId::fromString($command->getId()));
            $this->userRepository->save($user);
            $this->eventBus->dispatchAll($user);
        });
    }
}
