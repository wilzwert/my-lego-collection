<?php

namespace MyLegoCollection\SharedEvent\Event;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserCreatedIntegrationEvent extends IntegrationEvent
{
    private readonly string $id;
    private readonly string $entityId;

    private const string TYPE = 'user.user.created';

    public function __construct(string $id, string $entityId, ?array $payload = null, ?array $metadata = null)
    {
        $payload = array_merge(
            ['id' => $id, 'entityId' => $entityId],
            $payload ?? []
        );

        parent::__construct(self::TYPE, $payload, $metadata);
        $this->id = $id;
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
