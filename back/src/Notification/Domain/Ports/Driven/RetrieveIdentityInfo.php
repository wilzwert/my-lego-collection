<?php

namespace App\Notification\Domain\Ports\Driven;

use App\Notification\Domain\Model\IdentityInfo;

/**
 * @author Wilhelm Zwertvaegher
 */
interface RetrieveIdentityInfo
{
    public function getIdentityInfoFromId(string $identityId): ?IdentityInfo;

    public function getIdentityInfoFromUserId(string $userId): ?IdentityInfo;

}
