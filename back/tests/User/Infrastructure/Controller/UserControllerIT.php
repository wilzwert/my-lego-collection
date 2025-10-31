<?php

namespace App\Tests\User\Infrastructure\Controller;

use App\Tests\Bootstrap\WebTestCaseAuthenticateUser;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Wilhelm Zwertvaegher
 */

final class UserControllerIT extends WebTestCase
{
    use WebTestCaseAuthenticateUser;
    #[Test]
    public function shouldReturnAuthenticatedUser(): void
    {
        $client = self::createClient();
        $authenticatedUser = $this->authenticateUser($client);

        $client->request('GET', '/api/user/me');

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('email', $response);
        $this->assertSame($authenticatedUser->getDomainUser()->getEmail(), $response['email']);
    }

    #[Test]
    public function shouldReturn401_whenUnauthenticatedUser(): void
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
