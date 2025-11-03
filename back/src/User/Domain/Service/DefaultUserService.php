<?php

namespace App\User\Domain\Service;

use App\Shared\Domain\Model\Uuid;
use App\Shared\Domain\Service\TransactionProvider;
use App\Shared\Domain\Service\TransactionProviderException;
use App\User\Application\Command\CreateUserCommand;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;

readonly class DefaultUserService implements UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private TransactionProvider $transactionProvider,
    ) {
    }

    /**
     * @throws TransactionProviderException
     */
    public function createUser(CreateUserCommand $command): ?User
    {
        return $this->transactionProvider->transactional(function () use ($command) {
            $user = $this->userRepository->findByIdentityId(Uuid::fromString($command->identityId));
            if ($user) {
                return $user;
            }
            $user = new User(Uuid::generate(), Uuid::fromString($command->identityId), new \DateTimeImmutable(), new \DateTimeImmutable());
            $this->userRepository->save($user);

            return $user;
        });
    }

    public function getUserByIdentityId(Uuid $identityId): ?User
    {
        return $this->userRepository->findByIdentityId($identityId);
    }
}
