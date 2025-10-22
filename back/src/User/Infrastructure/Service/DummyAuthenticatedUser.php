<?php

namespace App\User\Infrastructure\Service;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final readonly class DummyAuthenticatedUser implements PasswordAuthenticatedUserInterface
{

    public function __construct(private string $password)
    {}

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
