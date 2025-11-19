<?php

namespace App\User\Domain\Service;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Command\DeleteAvatarCommand;
use App\User\Application\Command\UpdateAvatarCommand;
use App\User\Domain\Model\User;

interface UserService
{
    public function createUser(EntityId $identityId): ?User;

    public function updateAvatar(User $user, ?StoredFile $storedFile = null): User;

    public function getUserByIdentityId(EntityId $identityId): ?User;

    public function getUserById(EntityId $userId): ?User;
}
