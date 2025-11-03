<?php

namespace App\Tests\User\Infrastructure\Controller;

use App\Auth\Domain\Service\IdentityService;
use App\Auth\Infrastructure\Security\AuthenticatedUser;
use App\Tests\Shared\Traits\WebTestCaseAuthenticateUserTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Wilhelm Zwertvaegher
 */

final class UserControllerIT extends WebTestCase
{
    use WebTestCaseAuthenticateUserTrait;

    #[Test]
    public function shouldReturnAuthenticated(): void
    {
        $client = self::createClient();
        $authenticatedUser = $this->authenticateUser($client);

        $client->request('GET', '/api/user/me');

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $response);
    }

    #[Test]
    public function shouldReturn401_whenUnauthenticated(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/user/me');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function authenticatedUserNotFoundReturns404(): void
    {
        $client = self::createClient();
        $this->authenticateAsUnknownUser($client);

        $client->request('GET', '/api/user/me');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}

