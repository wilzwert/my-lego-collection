<?php

namespace App\Tests\Traits;

use App\Shared\Domain\Event\DomainEvent;
use App\Tests\Utilities\DummySyncHandler;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
use MyLegoCollection\SharedEvent\IntegrationEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * Provide useful methods for messenger related tests
 *
 * @author Wilhelm Zwertvaegher
 */
trait MessengerTestingTrait
{

    /**
     * @template T of DomainEvent
     * @param callable(array, array): T $factory
     * @return T
     */
    protected function createTrackableDomainEvent(callable $factory): DomainEvent
    {
        $payload = [];

        // passing an uniqid to allow checking that the message has been received by the dummy handler
        // counting messages or retrieving by class name is not reliable because we cannot ensure the handler
        // won't receive other messages
        $metadata = ['test_uniqid' => $uniqId = uniqid()];

        return $factory($payload, $metadata);
    }

    /**
     * @template T of IntegrationEvent
     * @template U of DomainEvent
     * @param TransportInterface $transport
     * @param DomainEvent $event
     * @param class-string<T>|class-string<U> $targetEventClass
     * @param ?callable(T|U): bool $check
     * @return T|U|null
     */
    protected function getTransportMatchingIntegrationEvent(
        TransportInterface $transport,
        DomainEvent $event,
        string $targetEventClass,
        ?callable $check = null
    ): IntegrationEvent|DomainEvent|null {
        do {
            $envelopesAsArray = $transport instanceof InMemoryTransport ? $transport->getSent() : iterator_to_array($transport->get());
            $asyncFirst = array_find(
                $envelopesAsArray,
                /**
                 * @template TT of IntegrationEvent|DomainEvent
                 * @param Envelope $env
                 * @return bool
                 */
                fn (Envelope $env) =>
                    ($e = $env->getMessage()) instanceof $targetEventClass
                    /** @var TT $e */
                    && $e->metadata()['test_uniqid'] === $event->metadata()['test_uniqid']
                    && (null === $check || $check($e))
            );
            if (null !== $asyncFirst) {
                return $asyncFirst->getMessage();
            }
        } while (count($envelopesAsArray) > 0);

        return null;
    }

    /**
     * @param DummySyncHandler $handler
     * @param IntegrationEvent $event
     * @return bool
     */
    protected function handlerContains(DummySyncHandler $handler, IntegrationEvent $event): bool
    {
        return array_any(
            $handler->getReceivedMessages(),
            fn ($receivedEvent) =>
                $receivedEvent instanceof ($event::class)
                && $receivedEvent->metadata()['test_uniqid'] === $event->metadata()['test_uniqid']
        );
    }

    /**
     * @return void
     */
    protected function resetMessenger(): void
    {
        $container = self::getContainer();

        $transports = ['messenger.transport.async'];
        foreach ($transports as $transport) {
            $actualTransport = $container->get($transport);
            if (method_exists($actualTransport, 'reset')) {
                $actualTransport->reset();
            } elseif (method_exists($actualTransport, 'get')) {
                iterator_to_array($actualTransport->get());
            }
        }

        $dummyHandler = $container->get(DummySyncHandler::class);
        $dummyHandler->reset();
    }
}
