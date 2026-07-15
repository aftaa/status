<?php

namespace App\Controller;

use App\Factory\LogListQueryFactory;
use App\Query\Log\GetLogByTaskQuery;
use App\Query\Log\GetLogListQuery;
use App\Service\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

class LogController extends AbstractController
{
    public function __construct(
        private final readonly QueryBus $queryBus,
        private final readonly LogListQueryFactory $logListQueryFactory,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/logs', name: 'app_logs')]
    public function index(Request $request): Response
    {
        $criteria = $this->logListQueryFactory->fromRequest($request);
        $result = $this->queryBus->dispatch(new GetLogListQuery($criteria));

        return $this->render('log/index.html.twig', [
            'result' => $result,
            'query' => $criteria,
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/log/{taskId}', name: 'app_log_task')]
    public function byTask(int $taskId): Response
    {
        $logs = $this->queryBus->dispatch(new GetLogByTaskQuery($taskId));

        return $this->render('log/task.html.twig', [
            'taskId' => $taskId,
            'logs' => $logs,
        ]);
    }
}
