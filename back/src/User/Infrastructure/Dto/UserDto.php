<?php

namespace App\User\Infrastructure\Dto;

use App\User\Domain\Entity\User;

final readonly class UserDto
{
    private string $email;

    private string $username;

    /**
     * @var list<string>
     */
    private array $roles;

    public function __construct(User $user)
    {
        $this->email = $user->getEmail();
        $this->username = $user->getUsername();
        $this->roles = $user->getRoles();
    }

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
