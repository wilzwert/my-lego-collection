<?php

namespace App\Tests\User\Infrastructure\MessageHandler;

use App\Tests\Traits\SliceInfraHandlersTrait;
use MyLegoCollection\SharedEvent\IdentityCreatedIntegrationEvent;
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
        self::assertHasEventHandlers('User', [IdentityCreatedIntegrationEvent::class]);
    }
}
