<?php

namespace App\Tests\User\Infrastructure\Messenging;

use App\User\Domain\Event\UserCreatedEvent;
use App\User\Domain\Model\User;
use App\User\Infrastructure\Messenging\UserIntegrationEventFactory;
use App\Shared\Domain\Model\EntityId;
use MyLegoCollection\SharedEvent\UserCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserIntegrationEventFactoryTest extends TestCase
{

    #[Test]
    public function shouldSupport(): void
    {
        $factory = new UserIntegrationEventFactory();
        self::assertTrue(
            $factory->supports(
                new UserCreatedEvent(new User(EntityId::generate(), 'test@example.com', 'test', 'hash'))));
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
        $entityId = EntityId::generate();

        $factory = new UserIntegrationEventFactory();
        $result = $factory->fromDomainEvent(new UserCreatedEvent(new User($entityId, 'test@example.com', 'test', 'hash')));

        self::assertInstanceOf(UserCreatedIntegrationEvent::class, $result);
        self::assertEquals($entityId->value(), $result->getId());

    }

    #[Test]
    public function shouldThrowExceptionWhenConvertingUnsupportedObject(): void
    {
        $factory = new UserIntegrationEventFactory();
        $this->expectException(\LogicException::class);
        $result = $factory->fromDomainEvent(new \stdClass());
    }

}
