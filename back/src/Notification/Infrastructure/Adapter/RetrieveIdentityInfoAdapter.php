<?php

namespace App\Notification\Infrastructure\Adapter;

use App\Notification\Domain\Model\IdentityInfo;
use App\Notification\Domain\Ports\Driven\RetrieveIdentityInfo;

/**
 * @author Wilhelm Zwertvaegher
 */
class RetrieveIdentityInfoAdapter implements RetrieveIdentityInfo
{

    public function getIdentityInfoFromId(string $identityId): ?IdentityInfo
    {
        // TODO: Implement getIdentityInfoFromId() method.
        return new IdentityInfo('identityId', 'test@example.com', 'username');
    }

    public function getIdentityInfoFromUserId(string $userId): ?IdentityInfo
    {
        // TODO: Implement getIdentityInfoFromUserId() method.
        return new IdentityInfo('identityId', 'test@example.com', 'username');
    }
}
