<?php

namespace App\Notification\Application\Port\Driven;

use MyLegoCollection\SharedContracts\Dto\UserDto;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveUserDto
{
    public function getUserDtoFromId(string $userId): ?UserDto;

}
