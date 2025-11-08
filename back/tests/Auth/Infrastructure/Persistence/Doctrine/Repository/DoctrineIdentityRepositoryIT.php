<?php

namespace App\Tests\Auth\Infrastructure\Persistence\Doctrine\Repository;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\Auth\Domain\Model\Identity;
use App\Auth\Infrastructure\Persistence\Doctrine\Repository\DoctrineIdentityRepository;
use App\Shared\Domain\Model\Uuid;
use Doctrine\ORM\EntityManagerInterface;

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
        $id = Uuid::generate();
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

        $this->assertNotNull($found);
        $this->assertSame('user@example.com', $found->getEmail());
        $this->assertSame('user123', $found->getUsername());
    }

    #[Test]
    public function shouldFindByUsername(): void
    {
        $found = $this->repository->findByUsername('user1');

        $this->assertNotNull($found);
        $this->assertSame('user1', $found->getUsername());
    }

    #[Test]
    public function shouldFindByEmail(): void
    {
        $found = $this->repository->findByUsername('user1');

        $this->assertNotNull($found);
        $this->assertSame('user1', $found->getUsername());
    }

    #[Test]
    public function shouldFindByEmailOrUsername(): void
    {
        $found = $this->repository->findByEmailOrUsername('user1@test.com', 'unknown');
        $found2 = $this->repository->findByEmailOrUsername('unknown', 'user1');
        $found3 = $this->repository->findByEmailOrUsername('user1@test.com', 'user1');

        $this->assertNotNull($found);
        $this->assertNotNull($found2);
        $this->assertNotNull($found3);
        $this->assertSame('user1', $found->getUsername());
        $this->assertSame('user1@test.com', $found->getEmail());
        $this->assertEquals($found, $found2);
        $this->assertEquals($found, $found3);
    }

    #[Test]
    public function shouldNotFindByEmailOrUsername(): void
    {
        $foundByEmail = $this->repository->findByEmailOrUsername('unknown@example.com', 'unknown');

        $this->assertNull($foundByEmail);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->entityManager);
    }
}
