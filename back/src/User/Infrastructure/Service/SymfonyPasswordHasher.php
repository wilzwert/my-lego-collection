<?php

namespace App\User\Infrastructure\Service;

use App\User\Domain\Service\PasswordHasher;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Autoconfigure]
readonly class SymfonyPasswordHasher implements PasswordHasher
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {}

    public function hash(string $plainPassword): string
    {
        return $this->passwordHasher->hashPassword(
            new DummyAuthenticatedUser($plainPassword),
            $plainPassword
        );
    }

    public function isValid(string $plainPassword, string $hashedPassword): bool
    {
        return $this->passwordHasher->isPasswordValid(new DummyAuthenticatedUser($hashedPassword), $plainPassword);
    }
}
