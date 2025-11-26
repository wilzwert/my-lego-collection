<?php

namespace App\Tests\User\Infrastructure\Messaging;

use App\Tests\User\Utilities\UserTestsUtility;
use App\User\Domain\Event\UserCreatedEvent;
use App\User\Domain\Model\User;
use App\User\Infrastructure\Messenger\UserIntegrationEventFactory;
use App\Shared\Domain\Model\EntityId;
use MyLegoCollection\SharedEvent\UserCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 *
 */

#[Group('Messenger')]
class UserIntegrationEventFactoryTest extends TestCase
{

    #[Test]
    public function shouldSupport(): void
    {
        $factory = new UserIntegrationEventFactory();
        self::assertTrue($factory->supports(new UserCreatedEvent(UserTestsUtility::generateUser())));
    }

    #[Test]
    public function shouldNotSupport(): void
    {
        $factory = new UserIntegrationEventFactory();
        self::assertFalse($factory->supports(new \stdClass()));
    }

    #[Test]
    public function shouldBuildIntegrationEvent(): void
    {
        $userId = EntityId::generate();

        $factory = new UserIntegrationEventFactory();
        $result = $factory->fromDomainEvent(
            new UserCreatedEvent(
                UserTestsUtility::generateUser(
                    userId: $userId
                ),
            )
        );

        self::assertInstanceOf(UserCreatedIntegrationEvent::class, $result);
        self::assertEquals($userId->value(), $result->getId());
    }

    #[Test]
    public function shouldThrowExceptionWhenConvertingUnsupportedObject(): void
    {
        $factory = new UserIntegrationEventFactory();
        $this->expectException(\LogicException::class);
        $result = $factory->fromDomainEvent(new \stdClass());
    }

}
