<?php

namespace App\CollectionManagement\Domain\Event;

use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\Shared\Domain\Event\DomainEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserSetCompletedEvent extends DomainEvent
{

    private const string TYPE = 'user.set.completed';

    /**
     * @param UserSet $userSet
     * @param array<string, string|int>|null $metadata
     */
    public function __construct(private readonly UserSet $userSet, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
    }

    public function getUserSet(): UserSet
    {
        return $this->userSet;
    }


}
