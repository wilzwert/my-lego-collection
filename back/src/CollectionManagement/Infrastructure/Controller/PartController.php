<?php

namespace App\CollectionManagement\Infrastructure\Controller;

use App\CollectionManagement\Application\Command\SearchPartQuery;
use App\CollectionManagement\Application\Handler\SearchPartHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/parts')]
class PartController extends AbstractController
{
    public function __construct(
        private readonly SearchPartHandler $searchPartHandler
    )
    {}

    #[Route('', methods: ['GET', 'HEAD'])]
    public function searchPart(Request $request): JsonResponse
    {
        if(!$request->query->has('q')) {
            throw new BadRequestHttpException();
        }
        $query = new SearchPartQuery($request->get('q'));
        return $this->json(($this->searchPartHandler)($query));
    }
}
