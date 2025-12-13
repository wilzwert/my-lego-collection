<?php

namespace App\DataFixtures;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        // we use a "real" entityId (see fixtures) because the Auth slice must be able to load it
        // to complete the Identity after user creation
        $user = new DoctrineUser()->fromDomain(
            new User(
                EntityId::fromString(TestData::USER1_ID),
                EntityId::fromString(TestData::IDENTITY1_ID),
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 12:30:00'),
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 14:45:00'),
                null
            )
        );

        $user2 = new DoctrineUser()->fromDomain(
            new User(
                EntityId::fromString(TestData::USER2_ID),
                EntityId::fromString(TestData::IDENTITY2_ID),
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-02 12:30:00'),
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-02 14:45:00'),
                null
            )
        );

        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AuthFixtures::class];
    }
}
