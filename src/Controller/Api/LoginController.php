<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class LoginController extends AbstractController
{
    #[OA\Post(
        path: '/api/login',
        description: 'Получить JWT-токен',
        requestBody: new OA\RequestBody(
            description: 'Учетные данные',
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'after@ya.ru'),
                    new OA\Property(property: 'password', type: 'string', example: 'darkside')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'JWT-токен',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Неверные учетные данные')
        ]
    )]
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): never
    {
        throw new \LogicException('This should never be reached!');
    }
}
