<?php

namespace App\Tests\User\Infrastructure\MessageHandler;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventHandler;
use App\Shared\Domain\Model\Uuid;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Handler\CreateUserHandler;
use App\User\Infrastructure\EventHandler\IdentityCreatedEventHandler;
use App\User\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
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

        $uuid = Uuid::generate();
        $domainEvent = new DomainEvent('auth.identity.created', $uuid->value());
        ($this->identityCreatedEventHandler)($domainEvent);

        $createdUser = $this->doctrineUserRepository->findByIdentityId($uuid);
        $this->assertNotNull($createdUser);
        $this->assertEquals($createdUser->getIdentityId(), $uuid);
    }
}
