<?php

namespace App\Auth\Infrastructure\Security;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Dummy authenticated user to allow Symfony's UserPasswordHasherInterface usage
 *
 * @author Wilhelm Zwertvaegher
 */
final readonly class DummyAuthenticatedUser implements PasswordAuthenticatedUserInterface
{

    public function __construct(private string $password)
    {}

    public function getPassword(): string
    {
        return $this->password;
    }
}
