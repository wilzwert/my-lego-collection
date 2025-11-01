<?php

namespace App\User\Domain\Entity;

use App\Shared\Domain\Uuid;

readonly class User
{
    /**
     * @param Uuid $id
     * @param string $email
     * @param string $username
     * @param string $passwordHash
     * @param list<string> $roles
     */
    public function __construct(
        private Uuid $id,
        public string $email,
        private string $username,
        private string $passwordHash,
        private array $roles = ['ROLE_USER']
    ) {}

    public function getId(): Uuid
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

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}

