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
        // TODO: actually Implement the getIdentityInfoFromId() method.
        return new IdentityInfo('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1', 'test@example.com', 'username');
    }

    public function getIdentityInfoFromUserId(string $userId): ?IdentityInfo
    {
        // TODO: actually Implement the getIdentityInfoFromUserId() method.
        return new IdentityInfo('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1', 'test@example.com', 'username');
    }
}
