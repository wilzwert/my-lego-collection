<?php

namespace App\User\Application\Command;

final readonly class RegisterUserCommand
{
    public function __construct(
        private readonly string $email,
        private readonly string $username,
        private readonly string $password
    )
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
