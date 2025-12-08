<?php

namespace MyLegoCollection\SharedContracts\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
class CompleteUserSetCommand extends Command
{
    private const string TYPE = 'collection.user.set.complete';

    public function __construct(private readonly string $userSetId, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
    }

    public function getUserSetId(): string
    {
        return $this->userSetId;
    }
}
