<?php

namespace App\User\Infrastructure\Controller;

use App\User\Application\Command\GetUserQuery;
use App\User\Application\Handler\GetUserHandler;
use App\User\Infrastructure\Dto\UserDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly GetUserHandler $getUserHandler,
        private readonly ObjectMapperInterface $objectMapper
    ) {
    }

    #[Route('/me', name: 'api_user_me', methods: ['GET'])]
    public function me(
        #[CurrentUser] ?UserInterface $user
    ) :JsonResponse {
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $retrievedUser = ($this->getUserHandler)(new GetUserQuery($user->getUserIdentifier()));
        if (!$retrievedUser) {
            throw new NotFoundHttpException();
        }
        return $this->json($this->objectMapper->map($retrievedUser, UserDto::class));
    }
}
