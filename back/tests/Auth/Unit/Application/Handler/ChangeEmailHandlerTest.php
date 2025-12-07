<?php

namespace App\Tests\Auth\Unit\Application\Handler;

use App\Auth\Application\Command\ChangeEmailCommand;
use App\Auth\Application\Handler\ChangeEmailHandler;
use App\Auth\Domain\Port\Driven\IdentityRepository;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Port\Driven\EventBus;
use App\Shared\Domain\Port\Driven\TransactionProvider;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class ChangeEmailHandlerTest extends TestCase
{
    private IdentityService&MockObject $identityService;
    private IdentityRepository&MockObject $identityRepository;

    private TransactionProvider&MockObject $transactionProvider;

    private EventBus&MockObject $eventBus;

    private ChangeEmailHandler $underTest;

    protected function setUp(): void
    {
        $this->identityService = $this->createMock(IdentityService::class);
        $this->identityRepository = $this->createMock(IdentityRepository::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->eventBus = $this->createMock(EventBus::class);
        $this->underTest = new ChangeEmailHandler(
            $this->identityService,
            $this->identityRepository,
            $this->transactionProvider,
            $this->eventBus
        );
    }


    #[Test]
    public function shouldChangeEmailWithinTransaction(): void
    {
        $identity = AuthTestsUtility::generateKnownIdentity();
        $identityIdAsString = $identity->getId()->value();
        $command = new ChangeEmailCommand($identityIdAsString, 'test@example.com');

        $this->identityRepository
            ->expects($this->once())
            ->method('findById')
            ->with(
                self::logicalAnd(
                    self::isInstanceOf(EntityId::class),
                    // implicitly using __toString on the passed EntityId to compare value
                    self::equalTo($identityIdAsString)
                )
            )
            ->willReturn($identity);

        $this->identityService
            ->expects($this->once())
            ->method('changeEmail')
            ->with($identity)
            ->willReturn($identity);

        $this->identityRepository
            ->expects($this->once())
            ->method('save')
            ->with($identity);

        $this->eventBus
            ->expects($this->once())
            ->method('dispatchAll')
            ->with($identity);

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
                // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $result = ($this->underTest)($command);

        self::assertSame($identity, $result);
    }

}
