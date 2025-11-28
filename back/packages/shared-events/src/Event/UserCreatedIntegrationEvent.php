<?php

namespace MyLegoCollection\SharedEvent\Event;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserCreatedIntegrationEvent extends IntegrationEvent
{

    private const string TYPE = 'user.user.created';

    public function __construct(private readonly string $id, private readonly string $entityId, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }
}
