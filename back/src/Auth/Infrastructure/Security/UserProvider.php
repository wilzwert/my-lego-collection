<?php

namespace App\Auth\Infrastructure\Security;

use App\Auth\Domain\Port\Driven\IdentityRepository;
use App\Auth\Infrastructure\Security\User\AuthenticatedUser;
use App\Shared\Domain\Exception\InvalidEntityIdException;
use App\Shared\Domain\Model\EntityId;
use Override;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Wilhelm Zwertvaegher
 * @implements UserProviderInterface<UserInterface>
 */
readonly class UserProvider implements UserProviderInterface
{
    public function __construct(private IdentityRepository $repository, private readonly LoggerInterface $logger)
    {
    }

    #[Override]
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $identity = $this->repository->findById(EntityId::fromString($identifier));
        } catch (InvalidEntityIdException $e) {
            $identity = $this->repository->findByIdentifier($identifier);
        }

        if (!$identity) {
            $this->logger->info("Identity '$identifier' not found.");
            throw new UserNotFoundException("Identity '$identifier' not found.");
        }
        $this->logger->info("Identity for '$identifier' found.");
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
