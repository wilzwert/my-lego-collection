<?php

namespace App\Auth\Infrastructure\Security;

use App\Auth\Domain\Model\Identity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class AuthenticatedUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(private Identity $identity)
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->identity->getId();
    }

    public function getRoles(): array
    {
        return $this->identity->getRoles();
    }

    public function getPassword(): ?string
    {
        return $this->identity->getPasswordHash();
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
    }
}
