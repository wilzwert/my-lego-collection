<?php

namespace App\Tests\Auth\Infrastructure\Controller;

use App\Auth\Domain\Service\IdentityService;
use App\Auth\Infrastructure\Security\AuthenticatedUser;
use App\Tests\Traits\WebTestCaseAuthenticateUserTrait;
use App\User\Domain\Repository\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Wilhelm Zwertvaegher
 */

final class AuthControllerIT extends WebTestCase
{
    use WebTestCaseAuthenticateUserTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
    }


    #[Test]
    public function shouldReturn204_whenRequestIsValid(): void
    {
        $this->client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'valid@example.com',
            'username' => 'validUsername',
            'password' => 'StrongPassword123!',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    #[Test]
    public function shouldReturn422_whenEmailInvalid(): void
    {
        $this->client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'invalid-email',
            'username' => 'validUsername',
            'password' => 'StrongPassword123!',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertStringContainsString('not a valid email', $response['errors']['email']['INVALID_FORMAT_ERROR']['invalid_format_error'] ?? '');
    }

    #[Test]
    public function shouldReturn422_whenUsernameContainsAt(): void
    {
        $this->client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'valid@example.com',
            'username' => 'invalid@username',
            'password' => 'StrongPassword123!',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString('The username can only include alphanumeric characters, underscores, and dashes', $response['errors']['username']['REGEX_FAILED_ERROR']['regex_failed_error'] ?? '');
    }

    #[Test]
    public function shouldReturn422_whenPasswordIsWeak(): void
    {
        $this->client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'valid@example.com',
            'username' => 'validUsername',
            'password' => '123',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString('password strength', $response['errors']['password']['PASSWORD_STRENGTH_ERROR']['password_strength_error'] ?? '');
    }

    #[Test]
    public function shouldReturnEmpty200_whenUserAlreadyAuthenticated(): void
    {
        // Simulate authenticated user
        $this->configureClient(self::AUTHENTICATED);

        $this->client->jsonRequest('POST', '/api/auth/registration', [
            'email' => 'new@example.com',
            'username' => 'newUser',
            'password' => 'StrongPassword123!',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSame('""', $this->client->getResponse()->getContent()); // empty
    }

    #[Test]
    public function shouldReturnAuthenticated(): void
    {
        $this->configureClient(self::AUTHENTICATED);
        $this->client->request('GET', '/api/auth/me');

        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('email', $response);
        self::assertSame('user1@test.com', $response['email']);
    }

    #[Test]
    public function shouldReturn401_whenUnauthenticated(): void
    {
        $this->client->request('GET', '/api/auth/me');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function authenticatedUserNotFoundReturns401(): void
    {
        $this->configureClient(self::UNKNOWN_AUTHENTICATED);

        $this->client->request('GET', '/api/auth/me');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}

