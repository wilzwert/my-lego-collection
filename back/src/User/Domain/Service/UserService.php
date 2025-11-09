<?php

namespace App\User\Domain\Service;

use App\Shared\Domain\Model\EntityId;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Command\DeleteAvatarCommand;
use App\User\Application\Command\UpdateAvatarCommand;
use App\User\Domain\Model\User;

interface UserService
{
    public function createUser(CreateUserCommand $command): ?User;

    public function updateAvatar(UpdateAvatarCommand $command): User;

    public function deleteAvatar(DeleteAvatarCommand $command): User;

    public function getUserByIdentityId(EntityId $identityId): ?User;

    public function getUserById(EntityId $userId): ?User;
}
