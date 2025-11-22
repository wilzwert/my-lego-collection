<?php

namespace MyLegoCollection\SharedEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedIntegrationEvent extends IntegrationEvent
{
    private readonly string $id;

    private const string TYPE = 'auth.identity.created';

    public function __construct(string $id)
    {
        parent::__construct(self::TYPE, ['id' => $id]);
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
