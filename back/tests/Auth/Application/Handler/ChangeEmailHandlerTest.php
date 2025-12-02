<?php

namespace App\Tests\Auth\Application\Handler;

use App\Auth\Application\Command\ChangeEmailCommand;
use App\Auth\Application\Handler\ChangeEmailHandler;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class ChangeEmailHandlerTest extends TestCase
{
    private IdentityRepository&MockObject $identityRepository;

    private TransactionProvider&MockObject $transactionProvider;

    private EventBus&MockObject $eventBus;

    private ChangeEmailHandler $underTest;

    protected function setUp(): void
    {
        $this->identityRepository = $this->createMock(IdentityRepository::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->eventBus = $this->createMock(EventBus::class);
        $this->underTest = new ChangeEmailHandler(
            $this->identityRepository,
            $this->transactionProvider,
            $this->eventBus
        );
    }

    #[Test]
    public function changeEmail_shouldDoNothingWhenEmailEqualsCurrentEmail(): void
    {
        $email = 'john@example.com';
        $identity = AuthTestsUtility::generateKnownIdentity(email: $email);
        $identityIdAsString = $identity->getId()->value();
        $command = new ChangeEmailCommand($identityIdAsString, $email);

        $this->identityRepository
            ->expects($this->once())
            ->method('findById')
            ->with($identityIdAsString)
            ->willReturn($identity);

        $this->identityRepository
            ->expects($this->never())
            ->method('findByIdentifier');

        $this->identityRepository
            ->expects($this->never())
            ->method('save');

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->never())
            ->method('transactional');

        $result = ($this->underTest)($command);

        self::assertSame($result, $identity);
    }


    #[Test]
    public function shouldChangeEmailWithinTransaction(): void
    {
        $email = 'john@example.com';
        $identity = AuthTestsUtility::generateKnownIdentity();
        $identityIdAsString = $identity->getId()->value();
        $command = new ChangeEmailCommand($identityIdAsString, $email);

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

        // simulate available email
        $this->identityRepository
            ->expects($this->once())
            ->method('findByIdentifier')
            ->with($email)
            ->willReturn(null);

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
                fn(callable $callback) => $callback()
            );

        $result = ($this->underTest)($command);

        self::assertSame($email, $result->getEmail());
    }

}
