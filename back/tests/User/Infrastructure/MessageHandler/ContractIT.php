<?php

namespace App\Tests\User\Infrastructure\MessageHandler;

use App\Tests\Shared\Traits\SliceInfraHandlersTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The User slice MUST handle IdentityCreatedEvent
 * @author Wilhelm Zwertvaegher
 */
class ContractIT extends KernelTestCase
{

    use SliceInfraHandlersTrait;

    #[Test]
    public function shouldHaveDomainEventHandlers(): void
    {
        $this->assertHasDomainEventHandlers('User', ['auth.identity.created']);
    }
}
