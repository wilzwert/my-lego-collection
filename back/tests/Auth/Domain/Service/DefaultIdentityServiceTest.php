<?php

namespace App\Tests\Auth\Domain\Service;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Domain\Service\DefaultIdentityService;
use App\Auth\Domain\Service\PasswordHasher;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use App\Auth\Application\Command\RegistrationCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class DefaultIdentityServiceTest extends TestCase
{
    private IdentityRepository&MockObject $identityRepository;
    private PasswordHasher&MockObject $passwordHasher;
    private TransactionProvider&MockObject $transactionProvider;
    private DefaultIdentityService $service;

    private EventBus&MockObject $eventBus;

    protected function setUp(): void
    {
        $this->identityRepository = $this->createMock(IdentityRepository::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->eventBus = $this->createMock(EventBus::class);

        $this->service = new DefaultIdentityService(
            $this->identityRepository,
            $this->passwordHasher,
            $this->transactionProvider,
            $this->eventBus
        );
    }

    #[Test]
    public function shouldCreateIdentityWithinTransaction(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');
        $hashedPassword = 'hashed-password';

        $this->identityRepository
            ->expects($this->once())
            ->method('findByEmailOrUsername')
            ->with($command->email, $command->username)
            ->willReturn(null);

        $this->passwordHasher
            ->expects($this->once())
            ->method('hash')
            ->with($command->password)
            ->willReturn($hashedPassword);

        $this->identityRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Identity::class));

        $eventId = null;
        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function (DomainEvent $event) use (&$eventId) {
                    self::assertSame('auth.identity.created', $event->type());
                    if (!$event instanceof IdentityCreatedEvent) {
                        throw new \LogicException('Event should be instance of IdentityCreatedEvent');
                    }
                    self::assertNotEmpty($event->getIdentity()->getId());
                    $eventId = $event->getIdentity()->getId()->value();
                    return true;
                })
            );

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
                // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $result = $this->service->createIdentity('john@example.com', 'john_doe', 'password');

        self::assertInstanceOf(Identity::class, $result);
        self::assertSame($command->email, $result->getEmail());
        self::assertSame($command->username, $result->getUsername());
        self::assertSame($eventId, $result->getId()->__toString());
    }

    #[Test]
    public function shouldThrowException_whenIdentityAlreadyExists(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');
        $existingUser = $this->createMock(Identity::class);

        $this->identityRepository
            ->method('findByEmailOrUsername')
            ->willReturn($existingUser);

        $this->transactionProvider
            ->method('transactional')
            ->willReturnCallback(fn (callable $callback) => $callback());

        $this->expectException(EntityAlreadyExistsException::class);

        $this->service->createIdentity('john@example.com', 'john_doe', 'password');
    }

    #[Test]
    public function shouldRetrieveIdentityByIdentifier(): void
    {
        $identifier = 'user-123';
        $expectedUser = $this->createMock(Identity::class);

        $this->identityRepository
            ->expects($this->once())
            ->method('findByIdentifier')
            ->with($identifier)
            ->willReturn($expectedUser);

        $result = $this->service->getIdentityByIdentifier($identifier);

        self::assertSame($expectedUser, $result);
    }
}
