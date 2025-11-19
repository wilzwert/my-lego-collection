<?php

namespace App\Auth\Application\Command;

final readonly class RegistrationCommand
{
    public function __construct(
        public string $email,
        public string $username,
        public string $password
    ) {
    }
}
