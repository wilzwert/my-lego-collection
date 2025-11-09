<?php

namespace App\User\Infrastructure\Controller;

use App\Auth\Application\Command\GetIdentityQuery;
use App\Auth\Infrastructure\Dto\IdentityDto;
use App\User\Application\Command\GetUserByIdentityQuery;
use App\User\Application\Handler\GetUserHandler;
use App\User\Infrastructure\Dto\UserDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly GetUserHandler $getUserHandler,
        private readonly ObjectMapperInterface $objectMapper
    ) {
    }


    #[Route('/me', name: 'api_user_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(
        #[CurrentUser] ?UserInterface $user
    ) :JsonResponse {
        $user = ($this->getUserHandler)(new GetUserByIdentityQuery($user->getUserIdentifier()));

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }
        return $this->json(
            $this->objectMapper->map(
                $user,
                UserDto::class
            )
        );
    }
}
