<?php

namespace MyLegoCollection\SharedEvent\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
class CreateUserCommand extends Command
{
    private readonly string $id;

    private const string TYPE = 'user.create';

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
