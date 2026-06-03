<?php

namespace App\Controller;

use App\Dto\TaskDto;
use App\Entity\Task;
use App\Enum\TaskOrder;
use App\Factory\TaskEventFactory;
use App\Factory\TaskFactory;
use App\Factory\TaskListQueryFactory;
use App\Form\TaskType;
use App\Service\TaskEventLogger;
use App\Service\TaskListerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task')]
final class TaskController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskListerInterface    $taskLister,
        private readonly TaskListQueryFactory   $queryFactory,
        private readonly TaskFactory            $taskFactory,
        private readonly TaskEventLogger        $taskEventLogger,
        private readonly TaskEventFactory       $taskEventFactory,
    ) {
    }

    #[Route('/', name: 'app_tasks')]
    public function index(Request $request): Response
    {
        $query = $this->queryFactory->fromRequest($request);

        $result = $this->taskLister->getFilteredAndSortedTasks($query);

        return $this->render('task/index.html.twig', [
            'result' => $result,
            'query' => $query,
            'taskOrderEnum' => TaskOrder::cases(),
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task): Response
    {
        $taskDto = new TaskDto(
            name: $task->getName() ?? '',
            status: $task->getStatus(),
        );

        $form = $this->createForm(TaskType::class, $taskDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskFactory->updateFromDto($task, $taskDto);
            $this->entityManager->flush();

            $this->taskEventLogger->log(
                $this->taskEventFactory->createFromTask('edit', $task, $taskDto)
            );

            return $this->redirectToRoute('app_tasks', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Task $task): Response
    {
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->getPayload()->getString('_token'))) {
            $this->taskEventLogger->log(
                $this->taskEventFactory->createFromTask('delete', $task)
            );

            $this->entityManager->remove($task);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_tasks', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $taskDto = new TaskDto();

        $form = $this->createForm(TaskType::class, $taskDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $this->taskFactory->createFromDto($taskDto);
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->taskEventLogger->log(
                $this->taskEventFactory->createFromTask('create', $task)
            );

            return $this->redirectToRoute('app_tasks', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/history', name: 'app_task_history', methods: ['GET'])]
    public function history(Task $task): Response
    {
        $events = $this->taskEventLogger->findByTaskId($task->getId());

        return $this->render('task/history.html.twig', [
            'task' => $task,
            'events' => $events,
        ]);
    }
}
