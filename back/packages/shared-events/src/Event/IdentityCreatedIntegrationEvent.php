<?php

namespace MyLegoCollection\SharedEvent\Event;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedIntegrationEvent extends IntegrationEvent
{
    private readonly string $id;

    private const string TYPE = 'auth.identity.created';

    public function __construct(string $id, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
