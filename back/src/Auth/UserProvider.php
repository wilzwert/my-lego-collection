<?php

namespace App\Auth;

use App\User\Domain\Repository\UserRepository;
use Override;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Wilhelm Zwertvaegher
 * @implements UserProviderInterface<UserInterface>
 */
readonly class UserProvider implements UserProviderInterface
{
    public function __construct(private UserRepository $repository)
    {
    }

    #[Override]
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->repository->findByIdentifier($identifier);

        if (!$user) {
            throw new UserNotFoundException("User '$identifier' not found.");
        }

        return new AuthenticatedUser($user);
    }

    #[Override]
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user; // JWT → stateless
    }

    #[Override]
    public function supportsClass(string $class): bool
    {
        return $class === AuthenticatedUser::class;
    }
}
