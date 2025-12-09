<?php

namespace App\Tests\Traits;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Port\Driven\IdentityRepository;
use App\Auth\Infrastructure\Security\User\AuthenticatedUser;
use App\DataFixtures\TestData;
use App\Shared\Domain\Model\EntityId;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\BrowserKit\Cookie;

trait WebTestCaseAuthenticateUserTrait
{

    protected function tearDown(): void
    {
        parent::tearDown();

        // reset client
        $this->configureClient(self::UNAUTHENTICATED);
    }


    private const int AUTHENTICATED = 1;
    private const int UNKNOWN_AUTHENTICATED = 2;
    private const int UNAUTHENTICATED = 3;


    private function configureClient(int $status): void
    {
        if (!isset($this->client)) {
            throw new \LogicException('Client should be set before calling configureClient()');
        }
        if ($status == self::UNAUTHENTICATED) {
            $this->client->getCookieJar()->clear();
        } else {
            [$token, $refreshToken] = $status === self::AUTHENTICATED ? $this->getAuthenticatedUserTokens() : $this->getUnknownAuthenticatedUserTokens();
            $this->client->getCookieJar()->set(new Cookie('token', $token));
        }
    }


    private function getAuthenticatedUserTokens(string $identifier = TestData::IDENTITY1_EMAIL) : array
    {
        $identityRepository = self::getContainer()->get(IdentityRepository::class);
        $testIdentity = $identityRepository->findByIdentifier($identifier);
        $authenticatedUser = new AuthenticatedUser($testIdentity);
        $jwtManager = self::getContainer()->get(JWTTokenManagerInterface::class);
        return [$jwtManager->create($authenticatedUser), 'TODO'];
    }


    private function getUnknownAuthenticatedUserTokens() : array
    {
        $unknownUser = new Identity(
            EntityId::fromString('00000000-0000-4000-a000-000000000000'),
            'unkown@example.com',
            'unknown',
            'password'
        );
        $unknownAuthenticatedUser = new AuthenticatedUser($unknownUser);
        $jwtManager = self::getContainer()->get(JWTTokenManagerInterface::class);
        return [$jwtManager->create($unknownAuthenticatedUser), 'TODO'];
    }
}
