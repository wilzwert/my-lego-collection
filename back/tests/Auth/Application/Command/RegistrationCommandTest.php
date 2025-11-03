<?php

namespace App\Tests\Auth\Application\Command;

use App\Auth\Application\Command\RegistrationCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class RegistrationCommandTest extends TestCase
{
    #[Test]
    public function shouldExposeProvidedValues(): void
    {
        $command = new RegistrationCommand('john@example.com', 'john_doe', 'secret');

        $this->assertSame('john@example.com', $command->email);
        $this->assertSame('john_doe', $command->username);
        $this->assertSame('secret', $command->password);
    }
}
