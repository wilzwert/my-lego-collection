<?php

namespace App\Auth\Application\Port;

use MyLegoCollection\SharedContracts\Dto\IdentityDto;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveIdentityDto
{
    public function getIdentityDtoFromId(string $identityId): ?IdentityDto;
}
