<?php

namespace App\Tests\Shared\Infrastructure\Security;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\Auth\Infrastructure\Security\User\AuthenticatedUser;
use App\Shared\Infrastructure\Security\JwtTokenGenerator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JwtTokenGeneratorTest extends TestCase
{
    private JWTTokenManagerInterface $jwtManager;
    private JwtTokenGenerator $generator;

    protected function setUp(): void
    {
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $this->generator = new JwtTokenGenerator($this->jwtManager);
    }

    #[Test]
    public function shouldGenerateTokenFromAuthenticatedUser(): void
    {
        $user = $this->createMock(AuthenticatedUser::class);
        $expectedToken = 'jwt.token.value';

        $this->jwtManager
            ->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn($expectedToken);

        $token = $this->generator->createToken($user);

        self::assertSame($expectedToken, $token);
    }

    #[Test]
    public function shouldPropagateExceptionThrownByJwtManager(): void
    {
        $user = $this->createMock(AuthenticatedUser::class);

        $this->jwtManager
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new \RuntimeException('JWT generation failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('JWT generation failed');

        $this->generator->createToken($user);
    }
}
