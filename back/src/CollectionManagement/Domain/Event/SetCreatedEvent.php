<?php

namespace App\CollectionManagement\Domain\Event;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\Shared\Domain\Event\DomainEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
class SetCreatedEvent extends DomainEvent
{

    private const string TYPE = 'set.created';

    /**
     * @param Set $set
     * @param array<string, string|int>|null $metadata
     */
    public function __construct(private readonly Set $set, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
    }

    public function getSet(): Set
    {
        return $this->set;
    }


}
