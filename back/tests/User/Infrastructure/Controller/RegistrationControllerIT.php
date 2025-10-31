<?php

namespace App\Tests\User\Infrastructure\Controller;

use App\Auth\AuthenticatedUser;
use App\User\Domain\Service\UserService;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use App\User\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Wilhelm Zwertvaegher
 */

final class RegistrationControllerIT extends WebTestCase
{
    #[Test]
    public function shouldReturn200_whenRequestIsValid(): void
    {
        $client = self::createClient();

        $client->jsonRequest('POST', '/api/auth/register', [
            'email' => 'valid@example.com',
            'username' => 'validUsername',
            'password' => 'StrongPassword123!',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    #[Test]
    public function shouldReturn422_whenEmailInvalid(): void
    {
        $client = self::createClient();

        $client->jsonRequest('POST', '/api/auth/register', [
            'email' => 'invalid-email',
            'username' => 'validUsername',
            'password' => 'StrongPassword123!',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('not a valid email', $response['violations'][0]['title'] ?? '');
    }

    #[Test]
    public function shouldReturn422_whenUsernameContainsAt(): void
    {
        $client = self::createClient();

        $client->jsonRequest('POST', '/api/auth/register', [
            'email' => 'valid@example.com',
            'username' => 'invalid@username',
            'password' => 'StrongPassword123!',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('cannot include spaces or', $response['violations'][0]['title'] ?? '');
    }

    #[Test]
    public function shouldReturn422_whenPasswordIsWeak(): void
    {
        $client = self::createClient();

        $client->jsonRequest('POST', '/api/auth/register', [
            'email' => 'valid@example.com',
            'username' => 'validUsername',
            'password' => '123',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('password strength', $response['violations'][0]['title'] ?? '');
    }

    #[Test]
    public function shouldReturnEmpty200_whenUserAlreadyAuthenticated(): void
    {
        // Simulate authenticated user
        // TODO : this should be done outside to make it reusable by others IT
        $client = self::createClient();
        $userService = self::getContainer()->get(UserService::class);
        $testUser = $userService->getUserByIdentifier('user1@test.com');
        $authenticatedUser = new AuthenticatedUser($testUser);
        $client->loginUser($authenticatedUser);


        $client->jsonRequest('POST', '/api/auth/register', [
            'email' => 'new@example.com',
            'username' => 'newUser',
            'password' => 'StrongPassword123!',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame('""', $client->getResponse()->getContent()); // empty
    }
}

