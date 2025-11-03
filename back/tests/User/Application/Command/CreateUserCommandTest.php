<?php

namespace App\Tests\User\Application\Command;

use App\Auth\Application\Command\RegistrationCommand;
use App\Shared\Domain\Model\Uuid;
use App\User\Application\Command\CreateUserCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class CreateUserCommandTest extends TestCase
{
    #[Test]
    public function shouldExposeProvidedValues(): void
    {
        $identityId = Uuid::generate();
        $command = new CreateUserCommand($identityId->__toString());

        $this->assertSame($identityId->__toString(), $command->identityId);
    }
}
