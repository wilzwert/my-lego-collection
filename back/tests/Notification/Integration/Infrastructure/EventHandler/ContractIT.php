<?php

namespace App\Tests\Notification\Integration\Infrastructure\EventHandler;

use App\Tests\Traits\SliceInfraHandlersTrait;
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
        self::assertHasMessageHandlers('Notification', [SendWelcomeNotificationCommand::class]);
    }
}
