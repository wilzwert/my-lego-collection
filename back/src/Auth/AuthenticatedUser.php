<?php

namespace App\Auth;

use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class AuthenticatedUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(private User $user)
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->user->getEmail();
    }

    public function getRoles(): array
    {
        return $this->user->getRoles();
    }

    public function getPassword(): ?string
    {
        return $this->user->getPasswordHash();
    }

    public function eraseCredentials(): void
    {
    }

    public function getDomainUser(): User
    {
        return $this->user;
    }
}
