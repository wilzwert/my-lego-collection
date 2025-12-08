<?php

namespace App\User\Application\Port;

use MyLegoCollection\SharedContracts\Dto\UserDto;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveUserDto
{
    public function getUserDtoFromId(string $userId): ?UserDto;
    public function getUserDtoFromIdentityId(string $identityId): ?UserDto;

}
