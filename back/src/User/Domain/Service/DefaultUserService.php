<?php

namespace App\User\Domain\Service;

use App\Shared\Domain\TransactionProvider;
use App\Shared\Domain\Uuid;
use App\User\Application\Command\RegisterUserCommand;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Repository\UserRepository;

class DefaultUserService implements UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $passwordHasher,
        private readonly TransactionProvider $transactionProvider
    )
    {}

    /**
     * @throws UserAlreadyExistsException
     */
    public function createUser(RegisterUserCommand $command): ?User
    {
        return $this->transactionProvider->transactional(function () use ($command) {
            $user = $this->userRepository->findByEmailOrUsername($command->getEmail(), $command->getUsername());
            if($user) {
                throw new UserAlreadyExistsException('User already exists');
            }
            $user = new User(Uuid::generate(), $command->getEmail(), $command->getUsername(), $this->passwordHasher->hash($command->getPassword()));
            $this->userRepository->save($user);
            return $user;
        });
    }

    public function getUserByIdentifier(string $identifier): ?User
    {
        return $this->userRepository->findByIdentifier($identifier);
    }
}
