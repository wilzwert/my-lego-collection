<?php

namespace App\User\Application\Command;

final readonly class GetUserQuery
{
    public function __construct(
        private string $identifier
    )
    {}

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
