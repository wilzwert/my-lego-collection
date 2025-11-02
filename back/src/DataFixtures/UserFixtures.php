<?php

namespace App\DataFixtures;

use App\Auth\Infrastructure\Persistence\Doctrine\Entity\DoctrineIdentity;
use App\Auth\Infrastructure\Security\DummyAuthenticatedUser;
use App\Shared\Domain\Model\Uuid;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {}

    public function load(ObjectManager $manager): void
    {
        // create a predictable and usable password
        $password = $this->hasher->hashPassword(new DummyAuthenticatedUser(''), 'Abcd_1234!');
        $id = Uuid::fromString('userId1');
        $user = new DoctrineIdentity($id, 'user1@test.com', 'user1', $password, ['USER']);
        $manager->persist($user);

        $manager->flush();
    }
}
