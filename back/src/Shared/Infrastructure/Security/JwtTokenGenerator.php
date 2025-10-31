<?php

namespace App\Shared\Infrastructure\Security;

use App\Auth\AuthenticatedUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

readonly class JwtTokenGenerator
{
    public function __construct(private JWTTokenManagerInterface $jwtManager)
    {
    }

    public function createToken(AuthenticatedUser $user): string
    {
        return $this->jwtManager->create($user);
    }
}
