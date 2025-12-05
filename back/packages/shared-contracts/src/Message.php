<?php

namespace MyLegoCollection\SharedEvent;

use Symfony\Component\Uid\Uuid;

/**
 * @author Wilhelm Zwertvaegher
 */
abstract class Message
{
    private readonly string $id;

    private readonly array $metadata;

    public function __construct(
        private readonly string $type,
        ?array $metadata = null
    ) {
        $this->id = Uuid::v4()->toString();
        $this->metadata = array_merge(
            array('occurred_at' => new \DateTimeImmutable()->format(DATE_ATOM)),
            $metadata ?? []
        );
    }

    public function type(): string
    {
        return $this->type;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }
}
