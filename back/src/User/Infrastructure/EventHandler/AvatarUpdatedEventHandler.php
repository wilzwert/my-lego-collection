<?php

namespace App\User\Infrastructure\EventHandler;

use App\Shared\Infrastructure\EventHandler\DomainEventHandler;
use App\User\Domain\Event\AvatarUpdatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements DomainEventHandler<AvatarUpdatedEvent>
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class AvatarUpdatedEventHandler implements DomainEventHandler
{
    public function __construct()
    {
    }


    public static function getMessageHandled(): string
    {
        return AvatarUpdatedEvent::class;
    }

    public function __invoke(AvatarUpdatedEvent $event): void
    {
        // do nothing for now
    }
}
