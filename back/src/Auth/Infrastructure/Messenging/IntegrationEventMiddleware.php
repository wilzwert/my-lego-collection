<?php

namespace App\Auth\Infrastructure\Messenging;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\Shared\Infrastructure\Messaging\IntegrationEventBus;
use MyLegoCollection\SharedEvent\IntegrationEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class IntegrationEventMiddleware implements MiddlewareInterface
{
    public function __construct(
        private IntegrationEventFactory $factory, private IntegrationEventBus $integrationBus
    ) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($this->factory->supports($message)) {
            return $stack->next()->handle($envelope, $stack);
        }

        $integrationEvent = $this->factory->fromDomainEvent($message);
        if($integrationEvent instanceof IntegrationEvent) {
            $this->integrationBus->dispatch($integrationEvent);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
