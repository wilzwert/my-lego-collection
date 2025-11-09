<?php

namespace App\User\Domain\Service;

use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\TransactionProvider;
use App\Shared\Domain\Service\TransactionProviderException;
use App\Shared\Domain\Service\UploadedFileStorageService;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Command\DeleteAvatarCommand;
use App\User\Application\Command\UpdateAvatarCommand;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;

readonly class DefaultUserService implements UserService
{
    private const FILE_TYPE = 'user.avatar';

    public function __construct(
        private UserRepository             $userRepository,
        private TransactionProvider        $transactionProvider,
        private UploadedFileStorageService $uploadedFileStorage,
    ) {
    }

    /**
     * @throws TransactionProviderException
     */
    public function createUser(CreateUserCommand $command): ?User
    {
        return $this->transactionProvider->transactional(function () use ($command) {
            $user = $this->userRepository->findByIdentityId(EntityId::fromString($command->identityId));
            if ($user) {
                return $user;
            }
            $user = new User(EntityId::generate(), EntityId::fromString($command->identityId), new \DateTimeImmutable(), new \DateTimeImmutable());
            $this->userRepository->save($user);

            return $user;
        });
    }

    public function getUserByIdentityId(EntityId $identityId): ?User
    {
        return $this->userRepository->findByIdentityId($identityId);
    }

    public function getUserById(EntityId $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    /**
     * @throws TransactionProviderException
     */
    public function updateAvatar(UpdateAvatarCommand $command): User
    {
        return $this->transactionProvider->transactional(function () use ($command) {
            $user = $this->userRepository->findByIdentityId(EntityId::fromString($command->identityId));
            if (!$user) {
                throw new EntityNotFoundException('User not found');
            }

            if($user->getAvatar()) {
                $this->uploadedFileStorage->delete($user->getAvatar());
                $user = $user->setAvatar(null);
            }

            $user = $user->setAvatar($this->uploadedFileStorage->upload($command->filepath, $command->filename, self::FILE_TYPE));

            $this->userRepository->save($user);

            return $user;
        });
    }

    public function deleteAvatar(DeleteAvatarCommand $command): User
    {
        // TODO: Implement deleteAvatar() method.
        return $this->transactionProvider->transactional(function () use ($command) {
            $user = $this->userRepository->findByIdentityId(EntityId::fromString($command->identityId));
            if (!$user) {
                throw new EntityNotFoundException('User not found');
            }

            if (!$user->getAvatar()) {
               return $user;
            }

            $this->uploadedFileStorage->delete($user->getAvatar());
            $user = $user->setAvatar(null);

            $this->userRepository->save($user);

            return $user;
        });
    }
}
