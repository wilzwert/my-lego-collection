<?php

namespace App\Notification\Application\Adapter;

use App\Notification\Application\Port\Driven\RetrieveIdentityDto;
use App\Notification\Application\Port\Driven\RetrieveUserDto;
use App\Notification\Domain\Model\IdentityInfo;
use App\Notification\Domain\Port\Driven\RetrieveIdentityInfo;
use MyLegoCollection\SharedEvent\Dto\IdentityDto;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class RetrieveIdentityInfoAdapter implements RetrieveIdentityInfo
{

    public function __construct(
        private RetrieveIdentityDto $retrieveIdentityDto,
        private RetrieveUserDto $retrieveUserDto
    )
    {
    }

    private function map(?IdentityDto $identityDto): ?IdentityInfo
    {
        return null === $identityDto ? null : new IdentityInfo($identityDto->getId(), $identityDto->getEmail(), $identityDto->getUsername());
    }

    public function getIdentityInfoFromId(string $identityId): ?IdentityInfo
    {
        return $this->map($this->retrieveIdentityDto->getIdentityDtoFromId($identityId));
    }

    public function getIdentityInfoFromUserId(string $userId): ?IdentityInfo
    {
        $userDto = $this->retrieveUserDto->getUserDtoFromId($userId);
        if (null === $userDto) {
            return null;
        }

        return $this->getIdentityInfoFromId($userDto->getIdentityId());
    }
}
