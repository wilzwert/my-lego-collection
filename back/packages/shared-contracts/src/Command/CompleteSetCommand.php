<?php

namespace MyLegoCollection\SharedContracts\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
class CompleteSetCommand extends Command
{
    private const string TYPE = 'collection.set.complete';

    public function __construct(private readonly string $setId, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
    }

    public function getSetId(): string
    {
        return $this->setId;
    }
}
