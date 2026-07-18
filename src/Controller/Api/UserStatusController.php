<?php

namespace App\Controller\Api;

use App\Command\User\UpdateUserStatusCommand;
use App\Entity\User;
use App\Query\Status\GetCurrentStatusQuery;
use App\Repository\StatusRepository;
use App\Service\CommandBus;
use App\Service\QueryBus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;

#[Route('/api/me', name: 'api_me_')]
class UserStatusController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CommandBus             $commandBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    #[OA\Get(
        path: '/api/me/status',
        description: 'Получить текущий статус',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Текущий статус',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'status',
                            ref: '#/components/schemas/Status',
                            nullable: true
                        )
                    ]
                )
            )
        ]
    )]
    #[Route('/status', name: 'get_status', methods: ['GET'])]
    public function getStatus(#[CurrentUser] User $user, QueryBus $bus): JsonResponse
    {
        return $this->json(
            ['status' => $bus->dispatch(new GetCurrentStatusQuery(user: $user))],
            context: ['groups' => 'status:read']
        );
    }

    #[OA\Put(
        path: '/api/me/status',
        description: 'Обновить текущий статус',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(
                        property: 'status',
                        description: 'Slug статуса',
                        type: 'string',
                        example: 'svoboden'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Статус обновлён',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Status updated'),
                        new OA\Property(
                            property: 'status',
                            ref: '#/components/schemas/Status'
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Не указан slug'),
            new OA\Response(response: 404, description: 'Статус не найден')
        ]
    )]
    #[Route('/status', name: 'update_status', methods: ['PUT'])]
    public function updateStatus(Request $request, #[CurrentUser] User $user): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $statusSlug = $data['status'] ?? null;

        if (!$statusSlug) {
            return $this->json(['error' => 'Status slug is required'], 400);
        }

        try {
            $result = $this->commandBus->dispatch(
                new UpdateUserStatusCommand($user->getId(), $statusSlug)
            );

            return $this->json($result);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (ExceptionInterface $e) {
        }
    }

    #[OA\Delete(
        path: '/api/me/status',
        description: 'Удалить текущий статус',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Статус удалён'),
            new OA\Response(response: 404, description: 'Статус не был установлен')
        ]
    )]
    #[Route('/status', name: 'delete_status', methods: ['DELETE'])]
    public function deleteStatus(#[CurrentUser] User $user): JsonResponse
    {
        if (!$user->getCurrentStatus()) {
            return $this->json(['error' => 'No status to delete'], 404);
        }

        $user->setCurrentStatus(null);
        $this->em->flush();

        return $this->json(['message' => 'Status deleted']);
    }
}
