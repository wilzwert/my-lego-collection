<?php

namespace App\Tests\Auth\Application\Handler;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Application\Handler\RegistrationHandler;
use App\Auth\Domain\Service\IdentityService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class RegistrationHandlerTest extends TestCase
{
    #[Test]
    public function shouldInvokeUserServiceToCreateUser(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'password');
        $identityService = $this->createMock(IdentityService::class);

        $identityService
            ->expects($this->once())
            ->method('createIdentity')
            ->with($command);

        $handler = new RegistrationHandler($identityService);

        $handler($command);
    }
}
