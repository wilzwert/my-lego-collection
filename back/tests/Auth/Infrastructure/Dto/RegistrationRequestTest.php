<?php

namespace App\Tests\Auth\Infrastructure\Dto;

use App\Auth\Infrastructure\Dto\RegistrationRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class RegistrationRequestTest extends TestCase
{
    #[Test]
    public function shouldExposeAllProvidedProperties(): void
    {
        $email = 'john@example.com';
        $username = 'john_doe';
        $password = 'StrongPassword123!';

        $request = new RegistrationRequest($email, $username, $password);

        $this->assertSame($email, $request->getEmail());
        $this->assertSame($username, $request->getUsername());
        $this->assertSame($password, $request->getPassword());
    }
}
