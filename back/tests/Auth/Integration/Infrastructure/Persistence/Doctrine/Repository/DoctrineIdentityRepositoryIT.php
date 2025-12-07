<?php

namespace App\Tests\Auth\Integration\Infrastructure\Persistence\Doctrine\Repository;

use App\Auth\Domain\Model\Identity;
use App\Auth\Infrastructure\Persistence\Doctrine\Repository\DoctrineIdentityRepository;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Wilhelm Zwertvaegher
 */


class DoctrineIdentityRepositoryIT extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private DoctrineIdentityRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(DoctrineIdentityRepository::class);

    }

    #[Test]
    public function shouldSaveAndFindByEmail(): void
    {
        $id = EntityId::generate();
        $identity = new Identity(
            $id,
            'user@example.com',
            'user123',
            'hashedPassword',
            ['ROLE_USER']
        );

        $this->repository->save($identity);
        $this->entityManager->flush();

        $found = $this->repository->findByEmail('user@example.com');

        self::assertNotNull($found);
        self::assertSame('user@example.com', $found->getEmail());
        self::assertSame('user123', $found->getUsername());
    }

    #[Test]
    public function shouldFindByUsername(): void
    {
        $found = $this->repository->findByUsername('user1');

        self::assertNotNull($found);
        self::assertSame('user1', $found->getUsername());
    }

    #[Test]
    public function shouldFindByEmail(): void
    {
        $found = $this->repository->findByUsername('user1');

        self::assertNotNull($found);
        self::assertSame('user1', $found->getUsername());
    }

    #[Test]
    public function shouldFindByEmailOrUsername(): void
    {
        $found = $this->repository->findByEmailOrUsername('user1@test.com', 'unknown');
        $found2 = $this->repository->findByEmailOrUsername('unknown', 'user1');
        $found3 = $this->repository->findByEmailOrUsername('user1@test.com', 'user1');

        self::assertNotNull($found);
        self::assertNotNull($found2);
        self::assertNotNull($found3);
        self::assertSame('user1', $found->getUsername());
        self::assertSame('user1@test.com', $found->getEmail());
        self::assertEquals($found, $found2);
        self::assertEquals($found, $found3);
    }

    #[Test]
    public function shouldNotFindByEmailOrUsername(): void
    {
        $foundByEmail = $this->repository->findByEmailOrUsername('unknown@example.com', 'unknown');

        self::assertNull($foundByEmail);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager);
    }
}
