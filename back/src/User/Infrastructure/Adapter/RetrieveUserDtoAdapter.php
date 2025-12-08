<?php

namespace App\User\Infrastructure\Adapter;

use App\Shared\Domain\Model\EntityId;
use App\User\Application\Port\RetrieveUserDto;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\UserRepository;
use MyLegoCollection\SharedContracts\Dto\UserDto;

/**
 * @author Wilhelm Zwertvaegher
 */
class RetrieveUserDtoAdapter implements RetrieveUserDto
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    private function map(?User $user): ?UserDto
    {
        return null === $user ? null : new UserDto($user->getId(), $user->getIdentityId());
    }

    public function getUserDtoFromId(string $userId): ?UserDto
    {
        return $this->map($this->userRepository->findById(EntityId::fromString($userId)));
    }

    public function getUserDtoFromIdentityId(string $identityId): ?UserDto
    {
        return $this->map($this->userRepository->findByIdentityId(EntityId::fromString($identityId)));
    }
}
