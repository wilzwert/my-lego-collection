<?php

namespace App\User\Application\Handler;

use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\StoredFileService;
use App\Shared\Domain\Service\TransactionProvider;
use App\User\Application\Command\UpdateAvatarCommand;
use App\User\Domain\Model\User;
use App\User\Domain\Service\UserService;

readonly class UpdateAvatarHandler
{
    public function __construct(
        private readonly TransactionProvider $transactionProvider,
        private readonly StoredFileService $storedFileService,
        private readonly UserService $userService,
        private readonly EventBus $eventBus
    ){
    }

    public function __invoke(UpdateAvatarCommand $command): User
    {
        return $this->transactionProvider->transactional(function () use ($command) {
            $user = $this->userService->getUserByIdentityId(EntityId::fromString($command->identityId));

            if (!$user) {
                throw new EntityNotFoundException('User not found');
            }

            $storedFile = $this->storedFileService->replace($user->getAvatar(), $command->tempFile, 'user.avatar');

            $updatedUser = $this->userService->updateAvatar($user, $storedFile);

            $this->eventBus->dispatchAll($updatedUser);

            return $updatedUser;
        });
    }
}
