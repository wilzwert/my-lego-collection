<?php

namespace App\User\Application\Handler;

use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\StoredFileService;
use App\Shared\Domain\Service\TransactionProvider;
use App\User\Application\Command\UpdateAvatarCommand;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\UserRepository;
use App\User\Domain\Service\UserService;

readonly class UpdateAvatarHandler
{
    public function __construct(
        private readonly TransactionProvider $transactionProvider,
        private readonly StoredFileService $storedFileService,
        private readonly UserRepository $userRepository,
        private readonly EventBus $eventBus
    ){
    }

    public function __invoke(UpdateAvatarCommand $command): User
    {
        return $this->transactionProvider->transactional(function () use ($command) {
            $user = $this->userRepository->findByIdentityId(EntityId::fromString($command->identityId));

            if (!$user) {
                throw new EntityNotFoundException('User not found');
            }

            $storedFile = $this->storedFileService->replace($user->getAvatar(), $command->tempFile, 'user.avatar');

            $updatedUser = $user->setAvatar($storedFile);

            $this->userRepository->save($updatedUser);

            $this->eventBus->dispatchAll($updatedUser);

            return $updatedUser;
        });
    }
}
