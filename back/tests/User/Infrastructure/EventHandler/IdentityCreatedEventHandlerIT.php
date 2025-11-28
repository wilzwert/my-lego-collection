<?php

namespace App\Tests\User\Infrastructure\EventHandler;

use App\Shared\Domain\Model\EntityId;
use App\User\Infrastructure\EventHandler\CreateUserCommandHandler;
use App\User\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserRepository;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
use MyLegoCollection\SharedEvent\Event\IdentityCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedEventHandlerIT extends KernelTestCase
{

    protected CreateUserCommandHandler $identityCreatedEventHandler;

    protected DoctrineUserRepository $doctrineUserRepository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->identityCreatedEventHandler = self::getContainer()->get(CreateUserCommandHandler::class);
        $this->doctrineUserRepository = self::getContainer()->get(DoctrineUserRepository::class);
    }

    #[Test]
    public function shouldCreateUser(): void
    {

        $entityId = EntityId::fromString('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1');
        $command = new CreateUserCommand($entityId->value());
        ($this->identityCreatedEventHandler)($command);

        $createdUser = $this->doctrineUserRepository->findByIdentityId($entityId);
        self::assertNotNull($createdUser);
        self::assertEquals($createdUser->getIdentityId(), $entityId);
    }
}
