<?php

namespace App\Tests\Auth\Infrastructure\Messaging;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Domain\Model\EntityId;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use App\Tests\Traits\MessengerTestingTrait;
use App\Tests\Utilities\DummySyncHandler;
use App\User\Infrastructure\EventHandler\IdentityCreatedIntegrationEventHandler;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
use MyLegoCollection\SharedEvent\IntegrationEvent;
use MyLegoCollection\SharedEvent\UserCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
#[Group('Messenger')]
class AuthIntegrationEventMiddleWareIT extends KernelTestCase
{

    use MessengerTestingTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->resetMessenger();
        parent::setUp();
    }

    #[Test]
    public function testDomainEventDispatchesIntegrationEvent(): void
    {
        $container = self::getContainer();

        /** @var DummySyncHandler $dummyHandler */
        $dummyHandler = $container->get(DummySyncHandler::class);

        /** @var MessageBusInterface $bus */
        $authBus = $container->get('auth.bus');

        /** @var TransportInterface $asyncTransport */
        $asyncTransport = $container->get('messenger.transport.async');

        // build a trackable DomainEvent to dispatch to the local slice bus
        $domainEvent = $this->createTrackableDomainEvent(
            fn (array $payload, array $metadata) =>
            new IdentityCreatedEvent(
                AuthTestsUtility::generateIdentity(
                    entityId: EntityId::fromString('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1')
                ),
                $payload,
                $metadata
            )
        );

        $authBus->dispatch($domainEvent);

        // IdentityCreatedIntegrationEvent must be sent on both sync and async transports
        $asyncEvent = $this->getTransportMatchingIntegrationEvent(
            $asyncTransport,
            $domainEvent,
            IdentityCreatedIntegrationEvent::class,
            fn (IdentityCreatedIntegrationEvent $event) => 'a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1' === $event->getId()
        );
        self::assertNotNull($asyncEvent);
        self::assertTrue($this->handlerContains($dummyHandler, $asyncEvent));
    }
}
