<?php

namespace App\Tests\Auth\Infrastructure\Controller;

use App\Auth\Domain\Service\IdentityService;
use App\Auth\Infrastructure\Security\AuthenticatedUser;
use App\Tests\Traits\WebTestCaseAuthenticateUserTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Wilhelm Zwertvaegher
 */

final class AuthControllerIT extends WebTestCase
{
    use WebTestCaseAuthenticateUserTrait;

    #[Test]
    public function shouldReturn204_whenRequestIsValid(): void
    {
        $client = self::createClient();

        $client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'valid@example.com',
            'username' => 'validUsername',
            'password' => 'StrongPassword123!',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    #[Test]
    public function shouldReturn422_whenEmailInvalid(): void
    {
        $client = self::createClient();

        $client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'invalid-email',
            'username' => 'validUsername',
            'password' => 'StrongPassword123!',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($client->getResponse()->getContent(), true);
        self::assertStringContainsString('not a valid email', $response['errors']['email']['INVALID_FORMAT_ERROR']['invalid_format_error'] ?? '');
    }

    #[Test]
    public function shouldReturn422_whenUsernameContainsAt(): void
    {
        $client = self::createClient();

        $client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'valid@example.com',
            'username' => 'invalid@username',
            'password' => 'StrongPassword123!',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertStringContainsString('The username can only include alphanumeric characters, underscores, and dashes', $response['errors']['username']['REGEX_FAILED_ERROR']['regex_failed_error'] ?? '');
    }

    #[Test]
    public function shouldReturn422_whenPasswordIsWeak(): void
    {
        $client = self::createClient();

        $client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'valid@example.com',
            'username' => 'validUsername',
            'password' => '123',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertStringContainsString('password strength', $response['errors']['password']['PASSWORD_STRENGTH_ERROR']['password_strength_error'] ?? '');
    }

    #[Test]
    public function shouldReturnEmpty200_whenUserAlreadyAuthenticated(): void
    {
        // Simulate authenticated user
        $client = self::createClient();
        $this->authenticateUser($client);

        $client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'new@example.com',
            'username' => 'newUser',
            'password' => 'StrongPassword123!',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSame('""', $client->getResponse()->getContent()); // empty
    }

    #[Test]
    public function shouldReturnAuthenticated(): void
    {
        $client = self::createClient();
        $authenticatedUser = $this->authenticateUser($client);

        $client->request('GET', '/api/auth/me');

        self::assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('email', $response);
        self::assertSame('user1@test.com', $response['email']);
    }

    #[Test]
    public function shouldReturn401_whenUnauthenticated(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/auth/me');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function authenticatedUserNotFoundReturns404(): void
    {
        $client = self::createClient();
        $this->authenticateAsUnknownUser($client);

        $client->request('GET', '/api/auth/me');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}

