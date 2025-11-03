<?php

namespace App\Tests\User\Domain\Service;

use App\Auth\Domain\Exception\IdentityAlreadyExistsException;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Domain\Service\DefaultIdentityService;
use App\Auth\Domain\Service\PasswordHasher;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Model\Uuid;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use App\Auth\Application\Command\RegistrationCommand;
use App\User\Application\Command\CreateUserCommand;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\DefaultUserService;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class DefaultUserServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private TransactionProvider $transactionProvider;
    private DefaultUserService $service;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);

        $this->service = new DefaultUserService(
            $this->userRepository,
            $this->transactionProvider
        );
    }

    #[Test]
    public function shouldCreateUserWithinTransaction(): void
    {
        $identityId = Uuid::generate();
        $command = new CreateUserCommand($identityId->__toString());
        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with($command->identityId)
            ->willReturn(null);

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
        $this->assertEquals($identityId, $result->getIdentityId());
    }

    #[Test]
    public function shouldRetrieveUserById(): void
    {
        $identityId = Uuid::generate();
        $expectedUser = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with($identityId)
            ->willReturn($expectedUser);

        $result = $this->service->getUserByIdentityId($identityId);

        $this->assertSame($expectedUser, $result);
    }
}
