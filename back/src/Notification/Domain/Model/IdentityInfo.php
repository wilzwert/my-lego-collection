<?php

namespace App\Notification\Domain\Model;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class IdentityInfo
{
    public function __construct(
        private readonly string $identityId,
        private readonly string $email,
        private readonly string $username
    ) {
    }

    public function getIdentityId(): string
    {
        return $this->identityId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
