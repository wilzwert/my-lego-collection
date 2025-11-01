<?php

namespace App\DataFixtures;

use App\Shared\Domain\Uuid;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use App\User\Infrastructure\Service\DummyAuthenticatedUser;
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
        $user = new DoctrineUser($id, 'user1@test.com', 'user1', $password, ['USER']);
        $manager->persist($user);

        $manager->flush();
    }
}
