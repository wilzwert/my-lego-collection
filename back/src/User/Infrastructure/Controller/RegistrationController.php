<?php

namespace App\User\Infrastructure\Controller;

use App\User\Application\Command\RegisterUserCommand;
use App\User\Application\Handler\RegisterUserHandler;
use App\User\Infrastructure\Dto\RegisterUserRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/auth/register')]
class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly RegisterUserHandler $registerUserHandler,
        private readonly ObjectMapperInterface $objectMapper,
    ) {
    }

    #[Route('', name: 'api_user_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterUserRequest $registerUserRequest,
        #[CurrentUser] ?UserInterface $user
    ) :JsonResponse {
        if ($user) {
            return $this->json('');
        }
        ($this->registerUserHandler)($this->objectMapper->map($registerUserRequest, RegisterUserCommand::class));
        return $this->json('');
    }
}
