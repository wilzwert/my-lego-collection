<?php

namespace App\Tests\Traits;

use App\Shared\Domain\Event\DomainEvent;
use App\Tests\Utilities\DummyHandler;
use App\Tests\Utilities\DummySyncCommandHandler;
use App\Tests\Utilities\DummySyncIntegrationEventHandler;
use MyLegoCollection\SharedContracts\Event\IntegrationEvent;
use MyLegoCollection\SharedContracts\Message;
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
     * @var array<int, array<Envelope>> $envelopesCache
     */
    private array $envelopesCache = [];

    protected function tearDown(): void
    {
        $this->envelopesCache = [];
        parent::tearDown();
    }


    /**
     * @template T of Message
     * @param callable(array): T $factory
     * @return T
     */
    protected function createTrackableMessage(callable $factory): Message
    {
        // passing an uniqid to allows checking that the message has been received by a dummy handler
        // counting messages or retrieving by class name is not reliable because we cannot ensure the handler
        // won't receive or dispatch other messages
        $metadata = ['test_uniqid' => $uniqId = uniqid()];

        return $factory($metadata);
    }

    /**
     * Fetch envelopes from a transport and store them in a local cache
     * This is useful because e.g. in case transport is async, fetching its envelopes with iterator_to_array(get())
     * also resets them
     *
     * @param TransportInterface $transport
     * @return array
     */
    private function getEnvelopes(TransportInterface $transport): array
    {
        $cacheId = spl_object_id($transport);
        if (!isset($this->envelopesCache[$cacheId])) {
            $this->envelopesCache[$cacheId] = [];
        }


        if ($transport instanceof InMemoryTransport) {
            $sent = $transport->getSent();
            $newEnvelopes = [];
            foreach ($sent as $envelope) {
                if (!isset($this->envelopesCache[$cacheId][spl_object_id($envelope)])) {
                    $newEnvelopes[spl_object_id($envelope)] = $envelope;
                }
            }
        } else {
            $newEnvelopes = [];
            do {
                $envelopesAsArray = iterator_to_array($transport->get());
                $newEnvelopes = array_merge($newEnvelopes, $envelopesAsArray);
            } while (count($envelopesAsArray) > 0);
        }

        $this->envelopesCache[$cacheId] = array_merge($this->envelopesCache[$cacheId], $newEnvelopes);

        return $this->envelopesCache[$cacheId];
    }

    /**
     * @template T of Message
     * @param TransportInterface $transport
     * @param T $message
     * @param class-string<T> $targetMessageClass
     * @param ?callable(T): bool $check
     * @return ?T
     */
    protected function getTransportMatchingMessage(
        TransportInterface $transport,
        Message        $message,
        string             $targetMessageClass,
        ?callable          $check = null
    ): ?Message {
        // get envelopes
        $envelopes = $this->getEnvelopes($transport);

        $asyncFirst = array_find(
            $envelopes,
            /**
             * @template TT of Message
             * @param Envelope $env
             * @return bool
             */
            fn (Envelope $env) =>
                ($e = $env->getMessage()) instanceof $targetMessageClass
                /** @var TT $e */
                && $e->metadata()['test_uniqid'] === $message->metadata()['test_uniqid']
                && (null === $check || $check($e))
        );

        return $asyncFirst?->getMessage();
    }

    /**
     * @param DummyHandler $handler
     * @param Message $event
     * @return bool
     */
    protected function handlerContains(DummyHandler $handler, Message $event): bool
    {
        return array_any(
            $handler->getReceivedMessages(),
            fn ($receivedEvent) =>
                $receivedEvent instanceof ($event::class)
                && $receivedEvent->metadata()['test_uniqid'] === $event->metadata()['test_uniqid']
        );
    }

    /**
     * Resets the current async transport and the tests handlers
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

        $container->get(DummySyncIntegrationEventHandler::class)->reset();
        $container->get(DummySyncCommandHandler::class)->reset();
    }
}
