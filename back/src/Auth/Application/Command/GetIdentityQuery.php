<?php

namespace App\Auth\Application\Command;

final readonly class GetIdentityQuery
{
    public function __construct(
        private string $id
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
