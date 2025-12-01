<?php

namespace App\Tests\Auth\Application\Handler;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Application\Handler\RegistrationHandler;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Service\EventBus;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class RegistrationHandlerTest extends TestCase
{
    #[Test]
    public function shouldInvokeIdentityServiceToCreateIdentity(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');

        $identity = AuthTestsUtility::generateIdentity();
        $identityService = $this->createMock(IdentityService::class);
        $eventBus = $this->createMock(EventBus::class);

        $identityService
            ->expects($this->once())
            ->method('createIdentity')
            ->with('john@example.com', 'john_doe', 'password')
            ->willReturn($identity);

        $eventBus
            ->expects($this->once())
            ->method('dispatchAll')
            ->with($identity);

        $handler = new RegistrationHandler($identityService, $eventBus);

        $handler($command);
    }
}
