<?php

namespace App\User\Domain\Repository;

use App\Shared\Domain\Uuid;
use App\User\Domain\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserRepository
{
    public function findByEmailOrUsername(string $email, string $username): ?User;

    public function findByIdentifier(string $identifier): ?User;

    public function findById(Uuid $uuid): ?User;

    public function save(User $user): void;
}
