<?php

namespace App\Tests\User\Infrastructure\Messaging;

use App\Shared\Domain\Model\EntityId;
use App\Tests\Traits\MessengerTestingTrait;
use App\Tests\User\Utilities\UserTestsUtility;
use App\Tests\Utilities\DummySyncIntegrationEventHandler;
use App\User\Domain\Event\UserCreatedEvent;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
#[Group('Messenger')]
class UserIntegrationEventIT extends KernelTestCase
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

        /** @var DummySyncIntegrationEventHandler $dummyHandler */
        $dummyHandler = $container->get(DummySyncIntegrationEventHandler::class);

        /** @var MessageBusInterface $bus */
        $userBus = $container->get('user.bus');

        /** @var TransportInterface $asyncTransport */
        $asyncTransport = $container->get('messenger.transport.async');

        // DomainEvent to dispatch to the local slice bus
        $domainEvent = $this->createTrackableMessage(
            fn (array $metadata) =>
                new UserCreatedEvent(
                    UserTestsUtility::generateUser(
                        identityId: EntityId::fromString('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1'),
                        userId: EntityId::fromString('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1')
                    ),
                    $metadata
                )
        );

        $userBus->dispatch($domainEvent);

        // UserCreatedIntegrationEvent must be sent on both sync and async transports
        $asyncEvent = $this->getTransportMatchingMessage(
            $asyncTransport,
            $domainEvent,
            UserCreatedIntegrationEvent::class,
            fn (UserCreatedIntegrationEvent $event) => 'a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1' === $event->getId()
        );
        self::assertNotNull($asyncEvent);
        self::assertTrue($this->handlerContains($dummyHandler, $asyncEvent));
    }
}
