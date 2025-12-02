<?php

namespace App\Tests\Auth\Application\Handler;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\EntityId;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserCreatedHandlerTest extends TestCase
{

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

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->never())
            ->method('transactional');

        $result = $this->service->completeIdentity(EntityId::fromString($identityIdAsString));

        self::assertSame($result, $identity);
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
            ->with($this->isInstanceOf(Identity::class));

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
            // simulate transaction -> just execute callback
                fn(callable $callback) => $callback()
            );

        $result = $this->service->completeIdentity(EntityId::fromString($identityIdAsString));

        self::assertSame(true, $result->isComplete());
    }

}
