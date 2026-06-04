<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository) {}

    #[OA\Get(
        path: '/api/users/search',
        description: 'Поиск пользователя по email или имени',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'q',
                description: 'Строка поиска (минимум 2 символа)',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'after')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Результаты поиска',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'users',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/User')
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Слишком короткий запрос')
        ]
    )]
    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return $this->json(['error' => 'Query must be at least 2 characters'], 400);
        }

        $users = $this->userRepository->search($query);

        return $this->json(
            ['users' => $users],
            context: ['groups' => 'user:read']
        );
    }

    #[OA\Get(
        path: '/api/users/{id}',
        description: 'Получить данные пользователя по ID',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID пользователя',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные пользователя',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/User'
                        )
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Пользователь не найден')
        ]
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        return $this->json(
            ['data' => $user],
            context: ['groups' => 'user:read']
        );
    }
}
