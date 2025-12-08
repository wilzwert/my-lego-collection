<?php

namespace App\CollectionManagement\Infrastructure\Controller;

use App\CollectionManagement\Application\Command\AddUserSetCommand;
use App\CollectionManagement\Application\Command\SearchSetQuery;
use App\CollectionManagement\Application\Handler\AddUserSetHandler;
use App\CollectionManagement\Application\Handler\SearchSetHandler;
use App\CollectionManagement\Infrastructure\Dto\AddUserSetRequest;
use App\Shared\Domain\Model\EntityId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/me/sets')]
final class UserSetController extends AbstractController
{
    public function __construct(
        private readonly AddUserSetHandler $addUserSetHandler
    ) {
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addSet(
        #[CurrentUser] ?UserInterface $user,
        #[MapRequestPayload] AddUserSetRequest $addUserSetRequest
    ): JsonResponse {
        $command = new AddUserSetCommand($addUserSetRequest->getExternalSetId(), $user->getUserIdentifier());
        return $this->json(($this->addUserSetHandler)($command));
    }
}
