<?php

namespace App\User\Domain\Entity;

final readonly class User
{
    public function __construct(
        private string $id,
        private string $email,
        private string $username,
        private string $passwordHash,
        private array $roles = ['ROLE_USER']
    ) {}

    public function getId(): string
    {
        return $this->id;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
    public function getRoles(): array
    {
        return $this->roles;
    }
}

