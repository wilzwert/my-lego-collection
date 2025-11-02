<?php

namespace App\Auth\Application\Command;

final readonly class GetIdentityQuery
{
    public function __construct(
        private string $identifier
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
