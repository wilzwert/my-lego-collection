<?php

namespace App\User\Infrastructure\Controller;

use App\Auth\Infrastructure\Dto\IdentityDto;
use App\User\Application\Command\GetUserQuery;
use App\User\Application\Handler\GetUserHandler;
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
        // TODO
        return $this->json('');
    }
}
