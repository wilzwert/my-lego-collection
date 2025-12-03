<?php

namespace App\Tests\User\Infrastructure\Controller;

use App\Tests\Traits\TestResourcesTrait;
use App\Tests\Traits\WebTestCaseAuthenticateUserTrait;
use App\User\Domain\Port\Driven\UserRepository;
use App\User\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Wilhelm Zwertvaegher
 */

final class UserControllerIT extends WebTestCase
{
    use WebTestCaseAuthenticateUserTrait;
    use TestResourcesTrait;

    private KernelBrowser $client;

    private readonly DoctrineUserRepository $doctrineUserRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $container = self::getContainer();
        $this->doctrineUserRepository = $container->get(UserRepository::class);
    }


    #[Test]
    public function shouldReturnAuthenticated(): void
    {
        $this->configureClient(self::AUTHENTICATED);
        $this->client->request('GET', '/api/user/me');
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('id', $response);
    }

    #[Test]
    public function shouldReturn401_whenUnauthenticated(): void
    {
        $this->configureClient(self::UNAUTHENTICATED);
        $this->client->request('GET', '/api/user/me');
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function authenticatedUserNotFoundReturns401(): void
    {
        $this->configureClient(self::UNKNOWN_AUTHENTICATED);
        $this->client->request('GET', '/api/user/me');
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function shouldUpdateAvatar(): void
    {
        $this->configureClient(self::AUTHENTICATED);
        $this->client->jsonRequest('PUT', '/api/user/me/avatar', [
            'filename' => 'avatar.png',
            'contents' => base64_encode(file_get_contents($this->getTestResourcePath('files/lego.png')))
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertEmpty($this->client->getResponse()->getContent());

        // get the user and check it now has an avatar
        // we do it in the same test because all tests are isolated in their own transaction
        // $this->authenticateUser($this->client);
        $this->client->request('GET', '/api/user/me');
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('id', $response);
        self::assertArrayHasKey('avatar', $response);
        self::assertSame('avatar.png', $response['avatar']['filename']);
        self::assertNotFalse(filter_var($response['avatar']['url'], FILTER_VALIDATE_URL));
    }
}
