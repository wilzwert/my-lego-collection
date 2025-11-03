<?php

namespace App\User\Application\Command;

final readonly class GetUserQuery
{
    public function __construct(
        private string $identityId
    ) {
    }

    public function getIdentityId(): string
    {
        return $this->identityId;
    }
}
