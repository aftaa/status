<?php

namespace App\Controller;

use App\Service\TaskEventLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LogController extends AbstractController
{
    public function __construct(
        public final TaskEventLogger $logger,
    ) {
    }

    #[Route('/logs', name: 'app_logs')]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(100, $request->query->getInt('limit', 20));

        $result = $this->logger->list(
            page: $page,
            limit: $limit,
        );

        return $this->render('log/index.html.twig', [
            'logs' => $result['items'],
            'page' => $page,
            'limit' => $limit,
            'total' => $result['total'],
            'totalPages' => ceil($result['total'] / $limit),
        ]);
    }

    #[Route('/log/{taskId}', name: 'app_log_task')]
    public function byTask(int $taskId): Response
    {
        $logs = $this->logger->findByTaskId($taskId);

        return $this->render('log/task.html.twig', [
            'taskId' => $taskId,
            'logs' => $logs,
        ]);
    }
}
