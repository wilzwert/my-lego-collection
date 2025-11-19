<?php

namespace App\Auth\Application\Command;

final readonly class GetIdentityQuery
{
    public function __construct(
        public string $id
    ) {
    }
}
