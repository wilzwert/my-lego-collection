<?php

namespace App\Auth\Infrastructure\Security;

use App\Auth\Domain\Repository\IdentityRepository;
use App\Shared\Domain\Model\EntityId;
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
    public function __construct(private IdentityRepository $repository)
    {
    }

    #[Override]
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $identity = $this->repository->findById(EntityId::fromString($identifier));

        if (!$identity) {
            throw new UserNotFoundException("Identity '$identifier' not found.");
        }

        return new AuthenticatedUser($identity);
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
