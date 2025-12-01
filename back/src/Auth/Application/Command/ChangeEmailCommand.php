<?php

namespace App\Auth\Application\Command;

final readonly class ChangeEmailCommand
{
    public function __construct(
        public string $identityId,
        public string $email
    ) {
    }
}
