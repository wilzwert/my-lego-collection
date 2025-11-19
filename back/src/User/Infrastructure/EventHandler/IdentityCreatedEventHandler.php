<?php

namespace App\User\Infrastructure\EventHandler;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventHandler;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Handler\CreateUserHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AsMessageHandler]
readonly class IdentityCreatedEventHandler implements DomainEventHandler
{
    public function __construct(private CreateUserHandler $createUserHandler)
    {
    }


    public static function getEventHandled(): string
    {
        return 'auth.identity.created';
    }

    public function __invoke(DomainEvent $event): void
    {
        if ($event->type() != self::getEventHandled()) {
            return;
        }

        ($this->createUserHandler)(new CreateUserCommand($event->id()));
    }
}
