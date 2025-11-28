<?php

namespace App\Auth\Domain\Event;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Event\DomainEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedEvent extends DomainEvent
{
    private const string TYPE = 'auth.identity.created';

    private readonly Identity $identity;

    public function __construct(Identity $identity, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
        $this->identity = $identity;
    }

    public function getIdentity(): Identity
    {
        return $this->identity;
    }
}
