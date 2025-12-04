<?php

namespace App\Tests\Auth\Integration\Infrastructure\EventHandler;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Tests\Traits\SliceInfraHandlersTrait;
use MyLegoCollection\SharedEvent\Event\UserCreatedIntegrationEvent;
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
    public function shouldHaveDomainEventHandlers(): void
    {
        self::assertHasMessageHandlers('Auth', [IdentityCreatedEvent::class, UserCreatedIntegrationEvent::class]);
    }
}
