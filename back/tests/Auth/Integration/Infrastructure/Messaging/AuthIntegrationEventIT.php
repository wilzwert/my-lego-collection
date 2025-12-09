<?php

namespace App\Tests\Auth\Integration\Infrastructure\Messaging;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use App\Tests\Traits\MessengerTestingTrait;
use MyLegoCollection\SharedContracts\Command\CreateUserCommand;
use MyLegoCollection\SharedContracts\Event\IdentityCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
#[Group('Messenger')]
class AuthIntegrationEventIT extends KernelTestCase
{

    use MessengerTestingTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->resetMessenger();
        parent::setUp();
    }

    #[Test]
    public function testMessagesDispatching(): void
    {
        $container = self::getContainer();

        /** @var MessageBusInterface $bus */
        $authBus = $container->get('auth.bus');

        /** @var TransportInterface $asyncTransport */
        $asyncTransport = $container->get('messenger.transport.async');

        $knownIdentity = AuthTestsUtility::generateKnownIdentity();

        // build a trackable DomainEvent to dispatch to the local slice bus
        $domainEvent = $this->createTrackableMessage(
            fn (array $metadata) =>
                new IdentityCreatedEvent(
                    $knownIdentity,
                    $metadata
                )
        );

        $authBus->dispatch($domainEvent);

        // IdentityCreatedIntegrationEvent must have be sent on async transports
        $asyncEvent = $this->getTransportMatchingMessage(
            $asyncTransport,
            IdentityCreatedIntegrationEvent::class,
            $domainEvent,
            fn (IdentityCreatedIntegrationEvent $event) => $knownIdentity->getId()->value() === $event->getIdentityId()
        );
        self::assertNotNull($asyncEvent);

        // a CreateUserCommand must have been sent on both sync and async transports
        $asyncCommand = $this->getTransportMatchingMessage(
            $asyncTransport,
            CreateUserCommand::class,
            $domainEvent,
            fn (CreateUserCommand $command) => $knownIdentity->getId()->value() === $command->getIdentityId()
        );
        self::assertNotNull($asyncCommand);
    }
}
