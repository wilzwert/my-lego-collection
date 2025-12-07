<?php

namespace App\Tests\Auth\Integration\Infrastructure\EventHandler;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Tests\Traits\SliceInfraHandlersTrait;
use MyLegoCollection\SharedContracts\Event\UserCreatedIntegrationEvent;
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
            'Auth',
            [
                IdentityCreatedEvent::class,
                IdentityCreatedEvent::class,
                UserCreatedIntegrationEvent::class
            ]
        );
    }
}
