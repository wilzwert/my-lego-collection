<?php

namespace App\Tests\Auth\Application\Handler;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Application\Handler\RegistrationHandler;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Port\PasswordHasher;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Domain\Service\DefaultIdentityService;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use App\Tests\Auth\Utilities\AuthTestsUtility;
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

    private IdentityService&MockObject $identityService;

    private EventBus&MockObject $eventBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->identityRepository = $this->createMock(IdentityRepository::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->identityService = $this->createMock(IdentityService::class);
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
        $identity = AuthTestsUtility::generateIdentity();
        $this->identityService
            ->expects(self::once())
            ->method('createIdentity')
            ->with($command->email, $command->username, $command->password)
            ->willReturn($identity);

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
                fn (callable $callback) => $callback()
            );

        ($this->underTest)($command);

        self::assertInstanceOf(Identity::class, $capture);
        self::assertSame($identity, $capture);
        self::assertSame($capture2, $capture);
    }


    #[Test]
    public function shouldThrowException_whenIdentityAlreadyExists(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');

        $this->identityService
            ->expects(self::once())
            ->method('createIdentity')
            ->with($command->email, $command->username, $command->password)
            ->willThrowException(new EntityAlreadyExistsException());

        $this->eventBus->expects(self::never())->method('dispatchAll');

        $this->transactionProvider
            ->method('transactional')
            ->willReturnCallback(fn (callable $callback) => $callback());

        $this->expectException(EntityAlreadyExistsException::class);

        ($this->underTest)($command);
    }
}
