<?php

namespace App\Tests\User\Integration\Infrastructure\EventHandler;

use App\Tests\Traits\SliceInfraHandlersTrait;
use App\User\Domain\Event\AvatarUpdatedEvent;
use App\User\Domain\Event\UserCreatedEvent;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
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
        self::assertHasMessageHandlers('User', [CreateUserCommand::class, UserCreatedEvent::class, AvatarUpdatedEvent::class]);
    }
}
