<?php

namespace App\Tests\Auth\Application\Handler;

use App\Auth\Application\Handler\UserCreatedHandler;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use App\Tests\Utilities\TestData;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserCreatedHandlerTest extends TestCase
{

    private IdentityRepository&MockObject $identityRepository;
    private TransactionProvider&MockObject $transactionProvider;

    private EventBus&MockObject $eventBus;

    private UserCreatedHandler $underTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->identityRepository = $this->createMock(IdentityRepository::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->eventBus = $this->createMock(EventBus::class);
        $this->underTest = new UserCreatedHandler(
            $this->identityRepository,
            $this->transactionProvider,
            $this->eventBus
        );
    }

    #[Test]
    public function complete_shouldDoNothingWhenCurrentIdentityAlreadyComplete(): void
    {
        $identity = AuthTestsUtility::generateKnownIdentity(isComplete: true);
        $identityIdAsString = $identity->getId()->value();

        $this->identityRepository
            ->expects($this->once())
            ->method('findById')
            ->with($identity->getId())
            ->willReturn($identity);

        $this->identityRepository
            ->expects($this->never())
            ->method('save');

        $this->eventBus
            ->expects($this->never())
            ->method('dispatchAll');

        $this->transactionProvider
            ->expects($this->never())
            ->method('transactional');

        ($this->underTest)(new UserCreatedIntegrationEvent(TestData::EXISTING_USER_ID, $identityIdAsString));
    }


    #[Test]
    public function shouldCompleteIdentityWithinTransaction(): void
    {
        $identity = AuthTestsUtility::generateKnownIdentity();
        $identityIdAsString = $identity->getId()->value();

        $this->identityRepository
            ->expects($this->once())
            ->method('findById')
            ->with($identityIdAsString)
            ->willReturn($identity);

        $this->identityRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($arg) use (&$savedIdentity) {
                $savedIdentity = $arg;
                return true;
            }));

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
                // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $this->eventBus
            ->expects($this->once())
            ->method('dispatchAll')
            ->with($this->callback(function ($arg) use (&$eventsIdentity) {
                $eventsIdentity = $arg;
                return true;
            }));

        ($this->underTest)(new UserCreatedIntegrationEvent(TestData::EXISTING_USER_ID, $identityIdAsString));

        self::assertSame(true, $savedIdentity->isComplete());
        self::assertSame($eventsIdentity, $savedIdentity);
    }
}
