<?php

namespace App\Tests\User\Infrastructure\Dto;

use App\User\Infrastructure\Dto\RegisterUserRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class RegisterUserRequestTest extends TestCase
{
    #[Test]
    public function shouldExposeAllProvidedProperties(): void
    {
        $email = 'john@example.com';
        $username = 'john_doe';
        $password = 'StrongPassword123!';

        $request = new RegisterUserRequest($email, $username, $password);

        $this->assertSame($email, $request->getEmail());
        $this->assertSame($username, $request->getUsername());
        $this->assertSame($password, $request->getPassword());
    }
}
