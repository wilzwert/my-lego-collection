<?php

namespace App\DataFixtures;

use App\Auth\Domain\Model\Identity;
use App\Auth\Infrastructure\Persistence\Doctrine\Entity\DoctrineIdentity;
use App\Auth\Infrastructure\Security\User\DummyAuthenticatedUser;
use App\Shared\Domain\Model\EntityId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @codeCoverageIgnore
 */
class IdentityFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {}

    public function load(ObjectManager $manager): void
    {
        // create a predictable and usable password
        $password = $this->hasher->hashPassword(new DummyAuthenticatedUser(''), 'Abcd_1234!');
        $identity = new DoctrineIdentity()->fromDomain(
            new Identity(
                id: EntityId::fromString('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1'),
                email:'user1@test.com',
                username: 'user1',
                passwordHash: $password,
                roles: ['ROLE_USER'],
                isComplete: false,
                validationToken: ''
            )
        );

        $identity2 = new DoctrineIdentity()->fromDomain(
            new Identity(
                id: EntityId::fromString('0efa63b0-3291-4da3-9dcc-0c7ea1d538d0'),
                email: 'user2@test.com',
                username: 'user2',
                passwordHash: $password,
                roles: ['ROLE_USER'],
                isComplete: false,
                validationToken: ''
            )
        );
        $manager->persist($identity);
        $manager->persist($identity2);
        $manager->flush();
    }
}
