<?php

namespace App\Notification\Application\Port\Driven;

use MyLegoCollection\SharedEvent\Dto\IdentityDto;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveIdentityDto
{
    public function getIdentityDtoFromId(string $identityId): ?IdentityDto;
}
