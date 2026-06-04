<?php

namespace App\Controller\Api;

use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/statuses', name: 'api_statuses_')]
class StatusController extends AbstractController
{
    public function __construct(private StatusRepository $repository) {}

    #[OA\Get(
        path: '/api/statuses',
        description: 'Получить список всех статусов',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список статусов',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Status')
                        ),
                        new OA\Property(property: 'total', type: 'integer', example: 5)
                    ]
                )
            )
        ]
    )]
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $statuses = $this->repository->findAll();

        return $this->json(
            ['data' => $statuses, 'total' => count($statuses)],
            context: ['groups' => 'status:read']
        );
    }

    #[OA\Get(
        path: '/api/statuses/{slug}',
        description: 'Получить статус по slug',
        parameters: [
            new OA\Parameter(
                name: 'slug',
                description: 'Slug статуса (например, svoboden)',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные статуса',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Status'
                        )
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Статус не найден')
        ]
    )]
    #[Route('/{slug}', name: 'show', methods: ['GET'])]
    public function show(string $slug): JsonResponse
    {
        $status = $this->repository->findOneBy(['slug' => $slug]);

        if (!$status) {
            return $this->json(['error' => 'Status not found'], 404);
        }

        return $this->json(
            ['data' => $status],
            context: ['groups' => 'status:read']
        );
    }
}
