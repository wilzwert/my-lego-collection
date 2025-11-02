<?php

namespace App\Tests\Shared\Traits;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\Auth\Infrastructure\Security\AuthenticatedUser;
use App\Shared\Domain\Model\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait WebTestCaseAuthenticateUserTrait
{

    private function authenticateUser(KernelBrowser $client) : AuthenticatedUser
    {
        $identityService = self::getContainer()->get(IdentityService::class);
        $testIdentity = $identityService->getIdentityByIdentifier('user1@test.com');
        $authenticatedUser = new AuthenticatedUser($testIdentity);
        $client->loginUser($authenticatedUser);
        return $authenticatedUser;
    }

    private function authenticateAsUnknownUser(KernelBrowser $client) : AuthenticatedUser
    {
        $unknownUser = new Identity(
            Uuid::fromString('unknown'),
            'unkown@example.com',
            'unknown',
            'password'
        );
        $unknownAuthenticatedUser = new AuthenticatedUser($unknownUser);
        $client->loginUser($unknownAuthenticatedUser);
        return $unknownAuthenticatedUser;
    }
}
