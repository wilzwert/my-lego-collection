<?php

namespace App\DataFixtures;

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
        $id = EntityId::fromString('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1');
        $identity = new DoctrineIdentity($id, 'user1@test.com', 'user1', $password, ['ROLE_USER']);
        $manager->persist($identity);
        $manager->flush();
    }
}
