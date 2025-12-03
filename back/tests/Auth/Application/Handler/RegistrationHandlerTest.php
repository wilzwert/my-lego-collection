<?php

namespace App\Tests\Auth\Application\Handler;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Application\Handler\RegistrationHandler;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Domain\Service\DefaultIdentityService;
use App\Auth\Domain\Service\IdentityService;
use App\Auth\Domain\Service\PasswordHasher;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use PHPUnit\Event\Event;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class RegistrationHandlerTest extends TestCase
{
    private IdentityRepository&MockObject $identityRepository;

    private TransactionProvider&MockObject $transactionProvider;

    private IdentityService $identityService;

    private EventBus&MockObject $eventBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->identityRepository = $this->createMock(IdentityRepository::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);

        $passwordHasher = $this->createMock(PasswordHasher::class);
        $passwordHasher->expects(self::any())->method('hash')->willReturn('hashed_password');
        $this->identityService = new DefaultIdentityService($passwordHasher);
        $this->eventBus = $this->createMock(EventBus::class);
        $this->underTest = new RegistrationHandler(
            $this->identityService,
            $this->identityRepository,
            $this->transactionProvider,
            $this->eventBus
        );
    }

    #[Test]
    public function shouldCreateIdentityWithinTransaction(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');

        $this->identityRepository
            ->expects($this->once())
            ->method('findByEmailOrUsername')
            ->with($command->email, $command->username)
            ->willReturn(null);

        $this->identityRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function ($arg) use (&$capture) {
                    $capture = $arg;
                    return true;
                })
            );


        $this->eventBus
            ->expects($this->once())
            ->method('dispatchAll')
            ->with(
                $this->callback(function ($arg) use (&$capture2) {
                    $capture2 = $arg;
                    return true;
                })
            );

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
            // simulate transaction -> just execute callback
                fn(callable $callback) => $callback()
            );


        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');
        ($this->underTest)($command);

        self::assertInstanceOf(Identity::class, $capture);
        self::assertSame($capture2, $capture);
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
            ->willReturnCallback(fn(callable $callback) => $callback());

        $this->expectException(EntityAlreadyExistsException::class);

        ($this->underTest)($command);
    }

    #[Test]
    public function shouldInvokeIdentityServiceToCreateIdentity(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');

        $identity = AuthTestsUtility::generateIdentity();

        $this->identityRepository
            ->expects($this->once())
            ->method('findByEmailOrUsername')
            ->with($command->email, $command->username)
            ->willReturn(null);

        $this->transactionProvider
            ->method('transactional')
            ->willReturnCallback(fn(callable $callback) => $callback());

        $identityService = $this->createMock(IdentityService::class);
        $eventBus = $this->createMock(EventBus::class);

        $identityService
            ->expects($this->once())
            ->method('createIdentity')
            ->with('john@example.com', 'john_doe', 'password')
            ->willReturn($identity);

        $eventBus
            ->expects($this->once())
            ->method('dispatchAll')
            ->with($identity);

        $handler = new RegistrationHandler($identityService, $this->identityRepository, $this->transactionProvider, $eventBus);

        $handler($command);
    }
}
