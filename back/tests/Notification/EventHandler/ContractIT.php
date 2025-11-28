<?php

namespace App\Tests\Notification\EventHandler;

use App\Tests\Traits\SliceInfraHandlersTrait;
use App\User\Domain\Event\UserCreatedEvent;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
use MyLegoCollection\SharedEvent\Command\SendWelcomeNotificationCommand;
use MyLegoCollection\SharedEvent\Event\IdentityCreatedIntegrationEvent;
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
    public function shouldHaveCommandHandlers(): void
    {
        self::assertHasMessageHandlers('Notification', [SendWelcomeNotificationCommand::class]);
    }
}
