<?php

namespace App\Notification\Application\Port\Driven;

use MyLegoCollection\SharedContracts\Dto\IdentityDto;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveIdentityDto
{
    public function getIdentityDtoFromId(string $identityId): ?IdentityDto;
}
