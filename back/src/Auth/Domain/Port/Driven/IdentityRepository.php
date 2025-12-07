<?php

namespace App\Auth\Domain\Port\Driven;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\EntityId;

interface IdentityRepository
{
    public function findByEmailOrUsername(string $email, string $username): ?Identity;

    public function findByIdentifier(string $identifier): ?Identity;

    public function findById(EntityId $id): ?Identity;

    public function findByEmail(string $email): ?Identity;

    public function save(Identity $identity): void;
}
