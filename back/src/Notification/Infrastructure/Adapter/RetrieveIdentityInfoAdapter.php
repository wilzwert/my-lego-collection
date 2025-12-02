<?php

namespace App\Notification\Infrastructure\Adapter;

use App\Notification\Application\Ports\Driven\RetrieveIdentityInfo;
use App\Notification\Domain\Model\IdentityInfo;

/**
 * @author Wilhelm Zwertvaegher
 */
class RetrieveIdentityInfoAdapter implements RetrieveIdentityInfo
{

    public function getIdentityInfoFromId(string $identityId): ?IdentityInfo
    {
        // TODO: Implement getIdentityInfoFromId() method.
        return new IdentityInfo('identityId', 'test@example.com');
    }

    public function getIdentityInfoFromUserId(string $userId): ?IdentityInfo
    {
        // TODO: Implement getIdentityInfoFromUserId() method.
        return new IdentityInfo('identityId', 'test@example.com');
    }
}
