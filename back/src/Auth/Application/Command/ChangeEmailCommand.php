<?php

namespace App\Auth\Application\Command;

final readonly class ChangeEmailCommand
{
    public function __construct(
        public string $id,
        public string $email
    ) {
    }
}
