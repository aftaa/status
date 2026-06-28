<?php

namespace App\Controller;

use App\Command\Task\CreateTaskCommand;
use App\Command\Task\DeleteTaskCommand;
use App\Command\Task\UpdateTaskCommand;
use App\Factory\TaskDtoFactory;
use App\Factory\TaskListQueryFactory;
use App\Form\TaskType;
use App\Query\Task\GetTaskForEditQuery;
use App\Query\Task\GetTaskListQuery;
use App\Service\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_tasks')]
    public function index(
        Request $request,
        TaskListQueryFactory $queryFactory,
        QueryBus $queryBus,
    ): Response {
        $query = $queryFactory->fromRequest($request);
        $result = $queryBus->dispatch(new GetTaskListQuery($query));

        return $this->render('task/index.html.twig', [
            'result' => $result,
            'query' => $query,
        ]);
    }

    #[Route('/task/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        TaskDtoFactory $taskDtoFactory,
        MessageBusInterface $commandBus,
    ): Response {
        $taskDto = $taskDtoFactory->createEmpty();
        $form = $this->createForm(TaskType::class, $taskDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandBus->dispatch(new CreateTaskCommand($taskDto));

            $this->addFlash('success', 'Задача создана!');
            return $this->redirectToRoute('app_tasks');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET'])]
    public function edit(
        int $id,
        QueryBus $queryBus,
    ): Response {
        $taskDto = $queryBus->dispatch(new GetTaskForEditQuery($id));

        $form = $this->createForm(TaskType::class, $taskDto);

        return $this->render('task/edit.html.twig', [
            'form' => $form,
            'taskId' => $id,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_update', methods: ['POST'])]
    public function update(
        Request $request,
        int $id,
        TaskDtoFactory $taskDtoFactory,
        MessageBusInterface $commandBus,
    ): Response {
        $taskDto = $taskDtoFactory->createFromRequest($request);
        $form = $this->createForm(TaskType::class, $taskDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandBus->dispatch(new UpdateTaskCommand($id, $taskDto));

            $this->addFlash('success', 'Задача обновлена!');
            return $this->redirectToRoute('app_tasks');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form,
            'taskId' => $id,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_task_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        int $id,
        MessageBusInterface $commandBus,
    ): Response {
        $tokenId = 'delete' . $id;
        if (!$this->isCsrfTokenValid($tokenId, $request->getPayload()->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $commandBus->dispatch(new DeleteTaskCommand($id));

        $this->addFlash('success', 'Задача удалена!');
        return $this->redirectToRoute('app_tasks');
    }
}
