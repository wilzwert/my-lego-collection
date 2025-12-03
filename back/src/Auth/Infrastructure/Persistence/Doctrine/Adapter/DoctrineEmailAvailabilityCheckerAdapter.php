<?php

namespace App\Auth\Infrastructure\Persistence\Doctrine\Adapter;

use App\Auth\Domain\Port\EmailAvailabilityChecker;
use App\Auth\Domain\Port\IdentityAvailabilityChecker;
use App\Auth\Domain\Repository\IdentityRepository;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DoctrineEmailAvailabilityCheckerAdapter implements EmailAvailabilityChecker
{
    public function __construct(private IdentityRepository $identityRepository)
    {
    }

    public function isEmailAvailable(string $email): bool
    {
        return $this->identityRepository->findByEmail($email) === null;
    }
}
