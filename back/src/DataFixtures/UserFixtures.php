<?php

namespace App\DataFixtures;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $id = EntityId::fromString('a2a2a2a2-a2a2-42a2-8a2a-a2a2a2a2a2a2');
        // we use a "real" entityId (see fixtures) because the Auth slice must be able to load it
        // to complete the Identity after user creation
        $user = new DoctrineUser()->fromDomain(
            new User(
                $id,
                EntityId::fromString('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1'),
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 12:30:00'),
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 14:45:00'),
                null
            )
        );

        $user2 = new DoctrineUser()->fromDomain(
            new User(
                EntityId::fromString('75e0d857-f490-426e-a186-ced38f536236'),
                EntityId::fromString('0efa63b0-3291-4da3-9dcc-0c7ea1d538d0'),
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-02 12:30:00'),
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-02 14:45:00'),
                null
            )
        );

        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
    }
}
