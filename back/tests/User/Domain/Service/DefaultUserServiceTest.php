<?php

namespace App\Tests\User\Domain\Service;

use App\Shared\Domain\TransactionProvider;
use App\Shared\Domain\Uuid;
use App\User\Application\Command\RegisterUserCommand;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\DefaultUserService;
use App\User\Domain\Service\PasswordHasher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class DefaultUserServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private PasswordHasher $passwordHasher;
    private TransactionProvider $transactionProvider;
    private DefaultUserService $service;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);

        $this->service = new DefaultUserService(
            $this->userRepository,
            $this->passwordHasher,
            $this->transactionProvider
        );
    }

    #[Test]
    public function shouldCreateUserWithinTransaction(): void
    {
        $command = new RegisterUserCommand('john@example.com', 'john_doe', 'password');
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
            ->with($this->isInstanceOf(User::class));

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
                // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $result = $this->service->createUser($command);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($command->email, $result->getEmail());
        $this->assertSame($command->username, $result->getUsername());
    }

    #[Test]
    public function shouldThrowException_whenUserAlreadyExists(): void
    {
        $command = new RegisterUserCommand('john@example.com', 'john_doe', 'password');
        $existingUser = $this->createMock(User::class);

        $this->userRepository
            ->method('findByEmailOrUsername')
            ->willReturn($existingUser);

        $this->transactionProvider
            ->method('transactional')
            ->willReturnCallback(fn (callable $callback) => $callback());

        $this->expectException(UserAlreadyExistsException::class);

        $this->service->createUser($command);
    }

    #[Test]
    public function shouldRetrieveUserByIdentifier(): void
    {
        $identifier = 'user-123';
        $expectedUser = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentifier')
            ->with($identifier)
            ->willReturn($expectedUser);

        $result = $this->service->getUserByIdentifier($identifier);

        $this->assertSame($expectedUser, $result);
    }
}
