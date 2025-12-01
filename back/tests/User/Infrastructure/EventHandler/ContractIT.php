<?php

namespace App\Tests\User\Infrastructure\EventHandler;

use App\Tests\Traits\SliceInfraHandlersTrait;
use App\User\Domain\Event\UserCreatedEvent;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
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
        self::assertHasMessageHandlers('User', [CreateUserCommand::class]);
    }

    #[Test]
    public function shouldHaveEventHandlers(): void
    {
        self::assertHasMessageHandlers('User', [UserCreatedEvent::class]);
    }
}
