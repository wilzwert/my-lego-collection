<?php

namespace App\User\Infrastructure\Dto;

use App\User\Domain\Entity\User;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: User::class)]
final class UserDto
{
    public string $email;

    public string $username;

    /**
     * @var list<string>
     */
    public array $roles;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
