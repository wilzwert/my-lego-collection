<?php

namespace App\Auth\Infrastructure\Messenger;

/**
 * Messenging middleware that delegates local DomainEvents to IntegrationEvents conversion
 * Converted events are then dispatched to the integration bus
 *
 * @author Wilhelm Zwertvaegher
 */

use App\Shared\Infrastructure\Messager\IntegrationEventBus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

readonly class AuthIntegrationEventMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthIntegrationEventFactory $factory,
        private IntegrationEventBus         $integrationBus,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($this->factory->supports($message)) {
            $this->logger->info('Converting and dispatching message of class['.$message::class.'] to integration bus.');
            $integrationEvent = $this->factory->fromDomainEvent($message);
            $this->integrationBus->dispatch($integrationEvent);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
