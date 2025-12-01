<?php

namespace MyLegoCollection\SharedEvent\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
class CreateUserCommand extends Command
{
    private readonly string $id;

    private const string TYPE = 'user.create';

    public function __construct(string $id, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
