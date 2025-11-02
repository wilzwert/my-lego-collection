<?php

namespace App\Tests\Auth\Domain\Service;

use App\Auth\Domain\Exception\IdentityAlreadyExistsException;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Domain\Service\DefaultIdentityService;
use App\Auth\Domain\Service\PasswordHasher;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use App\Auth\Application\Command\RegistrationCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class DefaultIdentityServiceTest extends TestCase
{
    private IdentityRepository $userRepository;
    private PasswordHasher $passwordHasher;
    private TransactionProvider $transactionProvider;
    private DefaultIdentityService $service;

    private EventBus $eventBus;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(IdentityRepository::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->eventBus = $this->createMock(EventBus::class);

        $this->service = new DefaultIdentityService(
            $this->userRepository,
            $this->passwordHasher,
            $this->transactionProvider,
            $this->eventBus
        );
    }

    #[Test]
    public function shouldCreateUserWithinTransaction(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');
        $hashedPassword = 'hashed-password';

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmailOrUsername')
            ->with($command->email, $command->username)
            ->willReturn(null);

        $this->passwordHasher
            ->expects($this->once())
            ->method('hash')
            ->with($command->password)
            ->willReturn($hashedPassword);

        $this->userRepository
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

        $result = $this->service->createIdentity($command);

        $this->assertInstanceOf(Identity::class, $result);
        $this->assertSame($command->email, $result->getEmail());
        $this->assertSame($command->username, $result->getUsername());
    }

    #[Test]
    public function shouldThrowException_whenUserAlreadyExists(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');
        $existingUser = $this->createMock(Identity::class);

        $this->userRepository
            ->method('findByEmailOrUsername')
            ->willReturn($existingUser);

        $this->transactionProvider
            ->method('transactional')
            ->willReturnCallback(fn (callable $callback) => $callback());

        $this->expectException(IdentityAlreadyExistsException::class);

        $this->service->createIdentity($command);
    }

    #[Test]
    public function shouldRetrieveUserByIdentifier(): void
    {
        $identifier = 'user-123';
        $expectedUser = $this->createMock(Identity::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentifier')
            ->with($identifier)
            ->willReturn($expectedUser);

        $result = $this->service->getIdentityByIdentifier($identifier);

        $this->assertSame($expectedUser, $result);
    }
}
