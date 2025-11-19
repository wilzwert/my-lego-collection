<?php

namespace App\User\Application\Command;

final readonly class CreateUserCommand
{
    public function __construct(
        public string $identityId
    ) {
    }
}
