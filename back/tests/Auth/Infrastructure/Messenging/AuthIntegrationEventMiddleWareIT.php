<?php

namespace App\Tests\Auth\Infrastructure\Messenging;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Domain\Model\EntityId;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use App\Tests\Traits\ResetMessengerTransportsTrait;
use App\User\Infrastructure\EventHandler\IdentityCreatedIntegrationEventHandler;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class AuthIntegrationEventMiddleWareIT extends KernelTestCase
{

    use ResetMessengerTransportsTrait;

    #[Test]
    public function testDomainEventDispatchesIntegrationEvent(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->resetMessengerTransports();

        // replace the real handler by a spy to ensure it is actually called by the sync transport
        $spy = $this->getMockBuilder(IdentityCreatedIntegrationEventHandler::class)
            ->onlyMethods(['__invoke'])
            ->disableOriginalConstructor()
            ->getMock();

        $calledEvents = [];
        $spy->method('__invoke')
            ->willReturnCallback(function ($event) use (&$calledEvents) {
                $calledEvents[] = $event;     // spy
            });

        $container->set(IdentityCreatedIntegrationEventHandler::class, $spy);

        /** @var MessageBusInterface $bus */
        $authBus = $container->get('auth.bus');

        /** @var TransportInterface $asyncTransport */
        $asyncTransport = $container->get('messenger.transport.async');

        // DomainEvent to dispatch to the local slice bus
        $domainEvent = new IdentityCreatedEvent(
            AuthTestsUtility::generateIdentity(
                entityId: EntityId::fromString('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1')
            )
        );

        $authBus->dispatch($domainEvent);

        // IdentityCreatedIntegrationEvent must be sent on both sync and async transports
        $asyncEnvelopes = $asyncTransport->get();
        $asyncEnvelopesAsArray = iterator_to_array($asyncEnvelopes);
        $this->assertNotEmpty($asyncEnvelopesAsArray, 'Le message a été dispatché sur le transport async');
        $this->assertCount(1, $asyncEnvelopesAsArray);

        $asyncFirst = $asyncEnvelopesAsArray[0]->getMessage();
        $this->assertInstanceOf(IdentityCreatedIntegrationEvent::class, $asyncFirst);

        $this->assertCount(1, $calledEvents);
        $this->assertEquals($asyncFirst, $calledEvents[0]);
        self::assertEquals('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1', $asyncFirst->getId());
    }
}
