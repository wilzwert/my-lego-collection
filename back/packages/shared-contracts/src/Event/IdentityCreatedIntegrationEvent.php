<?php

namespace MyLegoCollection\SharedContracts\Event;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedIntegrationEvent extends IntegrationEvent
{
    private const string TYPE = 'auth.identity.created';

    public function __construct(private readonly string $identityId, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
    }

    public function getIdentityId(): string
    {
        return $this->identityId;
    }
}
