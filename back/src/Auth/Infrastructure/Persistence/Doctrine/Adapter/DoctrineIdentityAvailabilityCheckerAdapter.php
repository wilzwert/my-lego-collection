<?php

namespace App\Auth\Infrastructure\Persistence\Doctrine\Adapter;

use App\Auth\Domain\Port\Driven\IdentityAvailabilityChecker;
use App\Auth\Domain\Port\Driven\IdentityRepository;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DoctrineIdentityAvailabilityCheckerAdapter implements IdentityAvailabilityChecker
{
    public function __construct(private IdentityRepository $identityRepository)
    {
    }

    public function isIdentityAvailable(string $email, string $username): bool
    {
        return $this->identityRepository->findByEmailOrUsername($email, $username) === null;
    }
}
