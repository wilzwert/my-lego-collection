<?php

namespace App\Shared\Domain\Event;

/**
 * @author Wilhelm Zwertvaegher
 */
abstract class DomainEvent
{
    /**
     * @var array<string, mixed>
     */
    private readonly array $metadata;

    /**
     * @param string $type
     * @param array<string, mixed>|null $payload
     * @param array<string, mixed>|null $metadata
     */
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

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }
}
