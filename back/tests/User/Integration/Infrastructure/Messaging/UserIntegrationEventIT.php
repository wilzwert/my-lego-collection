<?php

namespace App\Tests\User\Integration\Infrastructure\Messaging;

use App\Shared\Domain\Exception\InvalidEntityIdException;
use App\Shared\Domain\Model\EntityId;
use App\Tests\Traits\MessengerTestingTrait;
use App\Tests\User\Utilities\UserTestsUtility;
use App\Tests\Utilities\DummySyncIntegrationEventHandler;
use App\User\Domain\Event\UserCreatedEvent;
use MyLegoCollection\SharedContracts\Event\UserCreatedIntegrationEvent;
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

        /** @var MessageBusInterface $bus */
        $userBus = $container->get('user.bus');

        /** @var TransportInterface $asyncTransport */
        $asyncTransport = $container->get('messenger.transport.async');

        // DomainEvent to dispatch to the local slice bus
        $domainEvent = $this->createTrackableMessage(
        /**
         * @throws InvalidEntityIdException
         */ fn (array $metadata) =>
                new UserCreatedEvent(
                    UserTestsUtility::generateUser(
                        userId: EntityId::fromString('a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1'),
                        identityId: EntityId::fromString('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1')
                    ),
                    $metadata
                )
        );

        $userBus->dispatch($domainEvent);

        // UserCreatedIntegrationEvent must be sent on async transport
        $asyncEvent = $this->getTransportMatchingMessage(
            $asyncTransport,
            $domainEvent,
            UserCreatedIntegrationEvent::class,
            fn (UserCreatedIntegrationEvent $event) => 'a1a1a1a1-a1a1-41a1-91a1-a1a1a1a1a1a1' === $event->getId()
        );
        self::assertNotNull($asyncEvent);
    }
}
