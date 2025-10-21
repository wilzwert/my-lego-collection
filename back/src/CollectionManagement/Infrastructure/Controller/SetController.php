<?php

namespace App\CollectionManagement\Infrastructure\Controller;

use App\CollectionManagement\Application\Command\SearchSetQuery;
use App\CollectionManagement\Application\Handler\SearchSetHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class SetController extends AbstractController
{
    public function __construct(
        private readonly SearchSetHandler $searchSetHandler
    )
    {}

    #[Route('/sets', methods: ['GET', 'HEAD'])]
    public function searchSet(Request $request): JsonResponse
    {
        if(!$request->query->has('q')) {
            throw new BadRequestHttpException();
        }
        $query = new SearchSetQuery($request->get('q'));
        return $this->json(($this->searchSetHandler)($query));
    }
}
