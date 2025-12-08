<?php

namespace App\CollectionManagement\Application\Adapter;

use App\CollectionManagement\Domain\Port\Driven\RetrieveUserId;
use App\Shared\Domain\Model\EntityId;
use App\User\Application\Port\RetrieveUserDto;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class RetrieveUserIdAdapter implements RetrieveUserId
{
    public function __construct(private RetrieveUserDto $retrieveUserDto)
    {
    }

    public function getUserId(EntityId $identityId): ?EntityId
    {
        $userDto = $this->retrieveUserDto->getUserDtoFromIdentityId($identityId);
        return EntityId::fromString($userDto->getId());
    }
}
