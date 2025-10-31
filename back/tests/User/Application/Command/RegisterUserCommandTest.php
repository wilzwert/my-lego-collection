<?php

namespace App\Tests\User\Application\Command;

use App\User\Application\Command\RegisterUserCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class RegisterUserCommandTest extends TestCase
{
    #[Test]
    public function shouldExposeProvidedValues(): void
    {
        $command = new RegisterUserCommand('john@example.com', 'john_doe', 'secret');

        $this->assertSame('john@example.com', $command->email);
        $this->assertSame('john_doe', $command->username);
        $this->assertSame('secret', $command->password);
    }
}
