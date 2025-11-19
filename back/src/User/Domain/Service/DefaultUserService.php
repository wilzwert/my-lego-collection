<?php

namespace App\User\Domain\Service;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Service\TransactionProvider;
use App\Shared\Domain\Service\TransactionProviderException;
use App\User\Application\Command\CreateUserCommand;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;

readonly class DefaultUserService implements UserService
{
    public function __construct(
        private UserRepository      $userRepository,
        private TransactionProvider $transactionProvider
    ) {
    }

    /**
     * @throws TransactionProviderException
     */
    public function createUser(EntityId $identityId): ?User
    {
        return $this->transactionProvider->transactional(function () use ($identityId) {
            $user = $this->userRepository->findByIdentityId(EntityId::fromString($identityId));
            if ($user) {
                return $user;
            }
            $user = new User(EntityId::generate(), EntityId::fromString($identityId), new \DateTimeImmutable(), new \DateTimeImmutable());
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
    public function updateAvatar(User $user, ?StoredFile $storedFile = null): User
    {
        $user = $user->setAvatar($storedFile);
        $this->userRepository->save($user);
        return $user;
    }
}
