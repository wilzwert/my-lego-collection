<?php
namespace MyLegoCollection\SharedEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
abstract class IntegrationEvent
{
    private readonly array $metadata;

    public function __construct(
        private readonly string $type,
        private readonly ?array $payload = null,
        ?array $metadata = null
    ) {
        $this->metadata = array_merge(
            array('occurred_at' => new \DateTimeImmutable()->format(DATE_ATOM)),
            $metadata ?? []
        );
    }

    public function type(): string
    {
        return $this->type;
    }

    public function payload(): array
    {
        return $this->payload;
    }
    public function metadata(): array
    {
        return $this->metadata;
    }
}
