<?php

namespace App\Auth\Infrastructure\Controller;

use App\Auth\Application\Command\GetIdentityQuery;
use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Application\Handler\GetIdentityHandler;
use App\Auth\Application\Handler\RegistrationHandler;
use App\Auth\Infrastructure\Dto\IdentityDto;
use App\Auth\Infrastructure\Dto\RegistrationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly RegistrationHandler   $registrationHandler,
        private readonly GetIdentityHandler   $getIdentityHandler,
        private readonly ObjectMapperInterface $objectMapper,
    ) {
    }

    #[Route('/registration', name: 'api_auth_registration', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegistrationRequest $registerUserRequest,
        #[CurrentUser] ?UserInterface            $user
    ) :JsonResponse {
        if ($user) {
            return $this->json('');
        }
        ($this->registrationHandler)($this->objectMapper->map($registerUserRequest, RegistrationCommand::class));
        return $this->json('');
    }

    #[Route('/me', name: 'api_auth_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(#[CurrentUser] ?UserInterface $user) :JsonResponse
    {
        $identity = ($this->getIdentityHandler)(new GetIdentityQuery($user->getUserIdentifier()));

        if (!$identity) {
            throw new NotFoundHttpException('User not found');
        }
        return $this->json(
            $this->objectMapper->map(
                $identity,
                IdentityDto::class
            )
        );
    }
}
