<?php

namespace App\Tests\User\Infrastructure\MessageHandler;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Model\EntityId;
use App\User\Infrastructure\EventHandler\IdentityCreatedEventHandler;
use App\User\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserRepository;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedEventHandlerIT extends KernelTestCase
{

    protected IdentityCreatedEventHandler $identityCreatedEventHandler;

    protected DoctrineUserRepository $doctrineUserRepository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->identityCreatedEventHandler = self::getContainer()->get(IdentityCreatedEventHandler::class);
        $this->doctrineUserRepository = self::getContainer()->get(DoctrineUserRepository::class);
    }

    #[Test]
    public function shouldCreateUser(): void
    {

        $uuid = EntityId::generate();
        $integrationEvent = new IdentityCreatedIntegrationEvent($uuid->value());
        ($this->identityCreatedEventHandler)($integrationEvent);

        $createdUser = $this->doctrineUserRepository->findByIdentityId($uuid);
        self::assertNotNull($createdUser);
        self::assertEquals($createdUser->getIdentityId(), $uuid);
    }
}
