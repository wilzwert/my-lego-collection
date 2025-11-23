<?php

namespace App\Tests\Auth\Infrastructure\Messenging;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Auth\Infrastructure\Messenging\AuthIntegrationEventFactory;
use App\Shared\Domain\Model\EntityId;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
use MyLegoCollection\SharedEvent\IntegrationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class AuthIntegrationEventFactoryTest extends TestCase
{

    #[Test]
    public function shouldSupport(): void
    {
        $factory = new AuthIntegrationEventFactory();
        self::assertTrue($factory->supports(new IdentityCreatedEvent(new Identity(EntityId::generate(), 'test@example.com', 'test', 'hash'))));
    }

    #[Test]
    public function shouldNotSupport(): void
    {
        $factory = new AuthIntegrationEventFactory();
        self::assertFalse($factory->supports(new \stdClass()));
    }

    #[Test]
    public function shouldBuildIntegrationEvent(): void
    {
        $entityId = EntityId::generate();

        $factory = new AuthIntegrationEventFactory();
        $result = $factory->fromDomainEvent(new IdentityCreatedEvent(new Identity($entityId, 'test@example.com', 'test', 'hash')));

        self::assertInstanceOf(IdentityCreatedIntegrationEvent::class, $result);
        self::assertEquals($entityId->value(), $result->getId());

    }

    #[Test]
    public function shouldThrowExceptionWhenConvertingUnsupportedObject(): void
    {
        $factory = new AuthIntegrationEventFactory();
        $this->expectException(\LogicException::class);
        $result = $factory->fromDomainEvent(new \stdClass());
    }

}
