<?php

namespace MyLegoCollection\SharedEvent\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
class CreateUserCommand extends Command
{
    private const string TYPE = 'user.create';

    public function __construct(private readonly string $identityId, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
    }

    public function getIdentityId(): string
    {
        return $this->identityId;
    }
}
