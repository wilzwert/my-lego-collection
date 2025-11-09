<?php

namespace App\User\Infrastructure\Controller;

use App\Shared\Infrastructure\Service\Base64FileDecoder;
use App\User\Application\Command\GetUserByIdentityQuery;
use App\User\Application\Command\UpdateAvatarCommand;
use App\User\Application\Handler\GetUserHandler;
use App\User\Application\Handler\UpdateAvatarHandler;
use App\User\Infrastructure\Dto\UpdateAvatarRequest;
use App\User\Infrastructure\Dto\UserDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
        private readonly UpdateAvatarHandler $updateAvatarHandler,
        private readonly ObjectMapperInterface $objectMapper,
        private readonly Base64FileDecoder $base64FileDecoder
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

    #[Route('/me/avatar', name: 'api_user_me_avatar', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function avatar(
        #[MapRequestPayload] UpdateAvatarRequest $updateAvatarRequest,
        #[CurrentUser] ?UserInterface $user
    ) :JsonResponse {
        $tempFile = $this->base64FileDecoder->decodeToTempFile($updateAvatarRequest->getContents(), $updateAvatarRequest->getFileName());
        ($this->updateAvatarHandler)(new UpdateAvatarCommand($user->getUserIdentifier(), $tempFile));
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
