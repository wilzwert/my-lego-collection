<?php

namespace App\Tests\Bootstrap;

use App\Auth\AuthenticatedUser;
use App\Shared\Domain\Uuid;
use App\User\Domain\Entity\User;
use App\User\Domain\Service\UserService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait WebTestCaseAuthenticateUser
{

    private function authenticateUser(KernelBrowser $client) : AuthenticatedUser
    {
        // Crée un utilisateur test en base ou mock via fixtures
        // TODO : this should be done outside to make it reusable by others IT
        $userService = self::getContainer()->get(UserService::class);
        $testUser = $userService->getUserByIdentifier('user1@test.com');
        $authenticatedUser = new AuthenticatedUser($testUser);
        $client->loginUser($authenticatedUser);
        return $authenticatedUser;
    }

    private function authenticateAsUnknownUser(KernelBrowser $client) : AuthenticatedUser
    {
        $unknownUser = new User(
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
