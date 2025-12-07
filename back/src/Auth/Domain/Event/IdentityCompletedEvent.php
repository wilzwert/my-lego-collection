<?php

namespace App\Auth\Domain\Event;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Event\DomainEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCompletedEvent extends DomainEvent
{
    private const string TYPE = 'auth.identity.completed';

    private readonly Identity $identity;

    /**
     * @param Identity $identity
     * @param array<string, string|int>|null $metadata
     */
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
