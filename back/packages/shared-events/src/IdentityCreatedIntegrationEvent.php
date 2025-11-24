<?php

namespace MyLegoCollection\SharedEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedIntegrationEvent extends IntegrationEvent
{
    private readonly string $id;

    private const string TYPE = 'auth.identity.created';

    public function __construct(string $id, ?array $payload = null, ?array $metadata = null)
    {
        $payload = array_merge(
            ['id' => $id],
            $payload ?? []
        );

        parent::__construct(self::TYPE, $payload, $metadata);
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
