<?php

namespace MyLegoCollection\SharedEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
abstract class Message
{
    private readonly array $metadata;

    public function __construct(
        private readonly string $type,
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

    public function metadata(): array
    {
        return $this->metadata;
    }
}
