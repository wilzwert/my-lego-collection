<?php

namespace App\DataFixtures;

use App\Shared\Domain\Model\EntityId;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $id = EntityId::fromString('a2a2a2a2-a2a2-42a2-8a2a-a2a2a2a2a2a2');
        $entityId = EntityId::fromString('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1');
        $user = new DoctrineUser(
            $id,
            $entityId,
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 12:30:00'),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 14:45:00')
        );
        $manager->persist($user);
        $manager->flush();
    }
}
