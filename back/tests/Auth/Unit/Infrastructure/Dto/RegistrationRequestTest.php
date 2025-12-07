<?php

namespace App\Tests\Auth\Unit\Infrastructure\Dto;

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

        self::assertSame($email, $request->getEmail());
        self::assertSame($username, $request->getUsername());
        self::assertSame($password, $request->getPassword());
    }
}
