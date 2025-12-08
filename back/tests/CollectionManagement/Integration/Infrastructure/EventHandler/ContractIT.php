<?php

namespace App\Tests\CollectionManagement\Integration\Infrastructure\EventHandler;

use App\CollectionManagement\Domain\Event\SetCompletedEvent;
use App\CollectionManagement\Domain\Event\SetCreatedEvent;
use App\CollectionManagement\Domain\Event\UserSetCreatedEvent;
use App\Tests\Traits\SliceInfraHandlersTrait;
use MyLegoCollection\SharedContracts\Command\CompleteSetCommand;
use MyLegoCollection\SharedContracts\Command\CompleteUserSetCommand;
use MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The User slice MUST handle UserCreatedEvent
 * @author Wilhelm Zwertvaegher
 */
class ContractIT extends KernelTestCase
{

    use SliceInfraHandlersTrait;

    #[Test]
    public function shouldHaveMessageHandlers(): void
    {
        self::assertHasMessageHandlers(
            'CollectionManagement', [
                SetCreatedEvent::class,
                SetCompletedEvent::class,
                UserSetCreatedEvent::class,
                CompleteSetCommand::class,
                CompleteUserSetCommand::class
            ]
        );
    }
}
