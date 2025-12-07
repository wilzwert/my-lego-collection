<?php

namespace App\User\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;
use App\User\Domain\Model\User;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserCreatedEvent extends DomainEvent
{
    private const string TYPE = 'user.user.created';

    private readonly User $user;

    /**
     * @param User $user
     * @param array<string, string|int>|null $metadata
     */
    public function __construct(User $user, ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
