<?php

namespace App\Shared\Domain\Event;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class DomainEvent
{
    public function __construct(
        private string $type,
        private string $id,
        private ?array $payload = null,
        private ?array $metadata = null
    ) {
    }

    public function type(): string
    {
        return $this->type;
    }
    public function id(): string
    {
        return $this->id;
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
