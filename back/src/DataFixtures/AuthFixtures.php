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
class AuthFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {}

    public function load(ObjectManager $manager): void
    {
        // create a predictable and usable password
        $password = $this->hasher->hashPassword(new DummyAuthenticatedUser(''), 'Abcd_1234!');
        $identity = new DoctrineIdentity()->fromDomain(
            new Identity(
                id: EntityId::fromString(TestData::IDENTITY1_ID),
                email:TestData::IDENTITY1_EMAIL,
                username: TestData::IDENTITY1_USERNAME,
                passwordHash: $password,
                roles: ['ROLE_USER'],
                isComplete: false,
                validationToken: ''
            )
        );

        $identity2 = new DoctrineIdentity()->fromDomain(
            new Identity(
                id: EntityId::fromString(TestData::IDENTITY2_ID),
                email: TestData::IDENTITY2_EMAIL,
                username: TestData::IDENTITY2_USERNAME,
                passwordHash: $password,
                roles: ['ROLE_USER'],
                isComplete: false,
                validationToken: ''
            )
        );

        fwrite(STDOUT, "persist ".$identity->getEmail().PHP_EOL);

        $manager->persist($identity);
        $manager->persist($identity2);
        $manager->flush();
    }
}
