<?php

namespace App\Tests\EndToEnd;

use App\Auth\Domain\Port\Driven\IdentityRepository;
use App\Tests\Traits\WebTestCaseAuthenticateUserTrait;
use App\User\Domain\Port\Driven\UserRepository;
use App\User\Infrastructure\Dto\UserDto;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class RegistrationTest extends WebTestCase
{

    use WebTestCaseAuthenticateUserTrait;

    private const string USER_EMAIL = 'e2eregistration@example.com';
    private const string USER_USERNAME = 'e2eregistration';
    private const string USER_PASSWORD = 'StrongPassword123!';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    #[Test]
    public function shouldRegisterAndTriggerEvents(): void
    {
        $this->client->jsonRequest('POST', '/api/auth/registration', [
            'email' => self::USER_EMAIL,
            'username' => self::USER_USERNAME,
            'password' => self::USER_PASSWORD,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // at this point, the Identity entity MUST have been created
        /** @var \App\Auth\Domain\Port\Driven\IdentityRepository $identityRepository */
        $identityRepository = $this->client->getContainer()->get(IdentityRepository::class);
        $identity = $identityRepository->findByIdentifier(self::USER_EMAIL);
        self::assertNotNull($identity);

        // then, an event MUST have triggered the User creation command (synchronously)
        /** @var UserRepository $identityRepository */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findByIdentityId($identity->getId());
        self::assertNotNull($user);
        self::assertEquals($identity->getId(), $user->getIdentityId());

        // then login should be possible
        $this->client->jsonRequest('POST', '/api/login', ['email' => self::USER_EMAIL, 'password' => self::USER_PASSWORD]);

        // then GET /api/user/me should return the created User
        $this->client->jsonRequest('GET', '/api/user/me');

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        /** @var SerializerInterface $serializer */
        $serializer = self::getContainer()->get(SerializerInterface::class);

        /** @var UserDto $userDto */
        $userDto = $serializer->deserialize(
            $response->getContent(),
            UserDto::class,
            'json'
        );
        self::assertNotNull($userDto);
        self::assertEquals($user->getId(), $userDto->getId());
    }
}
