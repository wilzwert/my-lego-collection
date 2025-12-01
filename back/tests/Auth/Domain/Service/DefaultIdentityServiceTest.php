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
use App\Tests\Auth\Utilities\AuthTestsUtility;
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

    protected function setUp(): void
    {
        $this->identityRepository = $this->createMock(IdentityRepository::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);

        $this->service = new DefaultIdentityService(
            $this->identityRepository,
            $this->passwordHasher,
            $this->transactionProvider
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
    }

    #[Test]
    public function shouldThrowException_whenIdentityAlreadyExists(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');
        $existingIdentity = AuthTestsUtility::generateIdentity();

        $this->identityRepository
            ->method('findByEmailOrUsername')
            ->willReturn($existingIdentity);

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
        $expectedIdentity = AuthTestsUtility::generateIdentity();

        $this->identityRepository
            ->expects($this->once())
            ->method('findByIdentifier')
            ->with($identifier)
            ->willReturn($expectedIdentity);

        $result = $this->service->getIdentityByIdentifier($identifier);

        self::assertSame($expectedIdentity, $result);
    }
}
