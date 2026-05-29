=== src/Controller/TaskController.php ===
<?php

namespace App\Controller;

use App\Dto\TaskListQuery;
use App\Entity\Task;
use App\Enum\TaskSort;
use App\Factory\TaskListQueryFactory;
use App\Form\TaskType;
use App\Service\TaskListerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task')]
final class TaskController extends AbstractController
{
    #[Route('/', name: 'app_tasks')]
    public function index(Request $request, TaskListerInterface $taskLister, TaskListQueryFactory $queryFactory): Response
    {
        $query = $queryFactory->fromRequest($request);

        $result = $taskLister->getFilteredAndSortedTasks($query);

        return $this->render('task/index.html.twig', [
            'result' => $result,
            'query'  => $query,
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
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tasks', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tasks', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_tasks', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }
}
=== src/EventListener/TaskCacheListener.php ===
<?php

// src/EventListener/TaskCacheListener.php
namespace App\EventListener;

use App\Entity\Task;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TaskCacheListener
{
    public function __construct(
        private TagAwareCacheInterface $cache
    ) {}

    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->invalidate($args->getObject());
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->invalidate($args->getObject());
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->invalidate($args->getObject());
    }

    private function invalidate(object $entity): void
    {
        if ($entity instanceof Task) {
            $this->cache->invalidateTags(['tasks_collection']);
        }
    }
}
=== src/Factory/SpecificationFactory.php ===
<?php

namespace App\Factory;

use App\Specification\SpecificationInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class SpecificationFactory
{
    private array $specifications = [];

    public function __construct(
        #[TaggedIterator('app.task_specification')] iterable $specifications
    ) {
        foreach ($specifications as $specification) {
            $this->specifications[$specification->getKey()] = $specification;
        }
    }

    public function getSpecification(?string $key): ?SpecificationInterface
    {
        if ($key === null || $key === 'all') {
            return null;
        }

        return $this->specifications[$key] ?? null;
    }
}
=== src/Factory/SortingStrategyFactory.php ===
<?php

namespace App\Factory;

use App\Strategy\SortingStrategyInterface;

class SortingStrategyFactory
{
    /**
     * @var SortingStrategyInterface[]
     */
    private array $strategies = [];

    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            $this->strategies[$strategy->getKey()] = $strategy;
        }
    }

    public function getStrategy(string $type): SortingStrategyInterface
    {
        return $this->strategies[$type] ?? $this->strategies['created_at'];
    }
}
=== src/Factory/TaskListQueryFactory.php ===
<?php

namespace App\Factory;

use App\Dto\TaskListQuery;
use App\Enum\TaskFilter;
use App\Enum\TaskOrder;
use App\Enum\TaskSort;
use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use Symfony\Component\HttpFoundation\Request;

final class TaskListQueryFactory
{
    public function fromRequest(Request $request): TaskListQuery
    {
        return new TaskListQuery(
            sort:   TaskSort::tryFrom($request->query->get('sort', 'created_at')) ?? TaskSort::CREATED_AT,
            order:  TaskOrder::tryFrom($request->query->get('order', 'asc')) ?? TaskOrder::ASC,
            filter: TaskFilter::tryFrom($request->query->get('filter', 'all')) ?? TaskFilter::ALL,
            page:   new Page($request->query->get('page', 1)),
            limit:  new Limit($request->query->get('limit', 20)),
        );
    }
}
=== src/Form/TaskType.php ===
<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('is_completed', CheckboxType::class, [
                'label_attr' => ['class' => 'checkbox-switch'],
                'required' => false,
            ])
//            ->add('created_at', null, [
//                'widget' => 'single_text',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
=== src/Service/CachedTaskLister.php ===
<?php

namespace App\Service;

use App\Dto\PaginatedResult;
use App\Dto\TaskListQuery;
use App\Service\TaskListerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class CachedTaskLister implements TaskListerInterface
{
    public function __construct(
        private TaskListerInterface $delegate,
        private CacheInterface      $cache,
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getFilteredAndSortedTasks(TaskListQuery $query): PaginatedResult
    {
        $cacheKey = sprintf(
            'tasks_list_%s_%s_%s_p%s_l%s',
            $query->filter->value,
            $query->sort->value,
            $query->order->value,
            $query->page,
            $query->limit,
        );

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($query) {
            $item->expiresAfter(600);
            $item->tag(['tasks_collection']);

            return $this->delegate->getFilteredAndSortedTasks($query);
        });
    }
}
=== src/Service/DoctrinePaginator.php ===
<?php

namespace App\Service;

use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use Doctrine\ORM\QueryBuilder;
use App\Dto\PaginatedResult;

class DoctrinePaginator
{
    public function paginate(QueryBuilder $qb, Page $page, Limit $limit): PaginatedResult
    {
        $total = (clone $qb)->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $qb->setFirstResult(($page->number - 1) * $limit->value)
            ->setMaxResults($limit->value);

        return new PaginatedResult(
            items: $qb->getQuery()->getResult(),
            currentPage: $page->number,
            totalItems: (int) $total,
            itemsPerPage: $limit->value,
        );
    }
}
=== src/Service/TaskLister.php ===
<?php

namespace App\Service;

use App\Dto\PaginatedResult;
use App\Dto\TaskListQuery;
use App\Factory\SortingStrategyFactory;
use App\Factory\SpecificationFactory;
use App\Repository\TaskRepository;

readonly class TaskLister implements TaskListerInterface
{
    public function __construct(
        private TaskRepository         $taskRepository,
        private SortingStrategyFactory $sortingStrategyFactory,
        private SpecificationFactory   $specificationFactory,
        private DoctrinePaginator      $paginator,
    )
    {
    }

    public function getFilteredAndSortedTasks(TaskListQuery $query): PaginatedResult
    {
        $qb = $this->taskRepository->createQueryBuilder('t');

        $specification = $this->specificationFactory->getSpecification($query->filter->value);
        $specification?->apply($qb);

        $sortingStrategy = $this->sortingStrategyFactory->getStrategy($query->sort->value);
        $sortingStrategy?->apply($qb, $query->order->value);

        return $this->paginator->paginate($qb, $query->page, $query->limit);
    }
}
=== src/Service/TaskListerInterface.php ===
<?php

namespace App\Service;

use App\Dto\PaginatedResult;
use App\Dto\TaskListQuery;

interface TaskListerInterface
{
    public function getFilteredAndSortedTasks(TaskListQuery $query): PaginatedResult;
}
=== src/Dto/TaskListQuery.php ===
<?php

namespace App\Dto;

use App\Enum\TaskFilter;
use App\Enum\TaskOrder;
use App\Enum\TaskSort;
use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;

readonly class TaskListQuery
{
    public function __construct(
        public TaskSort   $sort = TaskSort::CREATED_AT,
        public TaskOrder  $order = TaskOrder::ASC,
        public TaskFilter $filter = TaskFilter::ALL,
        public Page       $page = new Page(1),
        public Limit      $limit = new Limit(10),
    ) {}
}
=== src/Dto/TaskListResult.php ===
<?php

namespace App\Dto;

readonly class TaskListResult
{
    public function __construct(
        public array         $tasks,
        public TaskListQuery $query,
    )
    {
    }
}
=== src/Dto/PaginatedResult.php ===
<?php

namespace App\Dto;

class PaginatedResult
{
    public function __construct(
        public readonly array $items,
        public readonly int $currentPage,
        public readonly int $totalItems,
        public readonly int $itemsPerPage,
    ) {}

    public function getTotalPages(): int
    {
        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }
}
=== src/Entity/Task.php ===
<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $is_completed = null;

    #[ORM\Column(type: 'datetime_immutable', updatable: false)]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->is_completed;
    }

    public function setIsCompleted(bool $is_completed): static
    {
        $this->is_completed = $is_completed;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
    }
}

// symfony console dbal:run-sql 'SELECT * FROM task'
=== src/Specification/SpecificationInterface.php ===
<?php
// src/Specification/SpecificationInterface.php

namespace App\Specification;

use Doctrine\ORM\QueryBuilder;

interface SpecificationInterface
{
    public function apply(QueryBuilder $qb): void;
    public function getKey(): string; // completed, not_completed, etc.
}
=== src/Specification/NotCompletedTasksSpecification.php ===
<?php
// src/Specification/NotCompletedTasksSpecification.php

namespace App\Specification;

use Doctrine\ORM\QueryBuilder;

class NotCompletedTasksSpecification implements SpecificationInterface
{
    public function apply(QueryBuilder $qb): void
    {
        $qb->andWhere('t.is_completed = :is_completed')
            ->setParameter('is_completed', false);
    }

    public function getKey(): string
    {
        return 'not_completed';
    }
}
=== src/Specification/CompletedTasksSpecification.php ===
<?php
// src/Specification/CompletedTasksSpecification.php

namespace App\Specification;

use Doctrine\ORM\QueryBuilder;

class CompletedTasksSpecification implements SpecificationInterface
{
    public function apply(QueryBuilder $qb): void
    {
        $qb->andWhere('t.is_completed = :is_completed')
            ->setParameter('is_completed', true);
    }

    public function getKey(): string
    {
        return 'completed';
    }
}
=== src/Strategy/SortByCreatedAtStrategy.php ===
<?php
// src/Strategy/SortByCreatedAtStrategy.php

namespace App\Strategy;

use Doctrine\ORM\QueryBuilder;

class SortByCreatedAtStrategy implements SortingStrategyInterface
{
    public function apply(QueryBuilder $qb, string $order): void
    {
        $qb->orderBy('t.created_at', $order);
    }

    public function getKey(): string
    {
        return 'created_at';
    }
}
=== src/Strategy/SortingStrategyInterface.php ===
<?php
// src/Strategy/SortingStrategyInterface.php

namespace App\Strategy;

use Doctrine\ORM\QueryBuilder;

interface SortingStrategyInterface
{
    public function apply(QueryBuilder $qb, string $order): void;
    public function getKey(): string;
}
=== src/Strategy/SortByNameStrategy.php ===
<?php
// src/Strategy/SortByNameStrategy.php

namespace App\Strategy;

use Doctrine\ORM\QueryBuilder;

class SortByNameStrategy implements SortingStrategyInterface
{
    public function apply(QueryBuilder $qb, string $order): void
    {
        $qb->orderBy('t.name', $order);
    }

    public function getKey(): string
    {
        return 'name';
    }
}
=== src/Enum/TaskFilter.php ===
<?php

namespace App\Enum;

enum TaskFilter: string
{
    case ALL = 'all';
    case COMPLETED = 'completed';
    case NOT_COMPLETED = 'not_completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::ALL => 'Все',
            self::COMPLETED => 'Выполненные',
            self::NOT_COMPLETED => 'Невыполненные',
        };
    }

    public function isAll(): bool
    {
        return $this === self::ALL;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isNotCompleted(): bool
    {
        return $this === self::NOT_COMPLETED;
    }
}
=== src/Enum/TaskStatus.php ===
<?php

// src/Enum/TaskStatus.php
namespace App\Enum;

enum TaskStatus: string
{
    case COMPLETED = 'completed';
    case NOT_COMPLETED = 'not_completed';

    public function getLabel(): string
    {
        return match($this) {
            self::COMPLETED => '✅ Выполнена',
            self::NOT_COMPLETED => '❌ Не выполнена',
        };
    }
}
=== src/Enum/TaskSort.php ===
<?php

namespace App\Enum;

enum TaskSort: string
{
    case CREATED_AT = 'created_at';
    case NAME = 'name';
    case IS_COMPLETED = 'is_completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATED_AT => 'По дате создания',
            self::NAME => 'По названию',
            self::IS_COMPLETED => 'По статусу',
        };
    }

    public function isCreatedAt(): bool
    {
        return $this === self::CREATED_AT;
    }

    public function isName(): bool
    {
        return $this === self::NAME;
    }

    public function isCompleted(): bool
    {
        return $this === self::IS_COMPLETED;
    }
}
=== src/Enum/TaskOrder.php ===
<?php

namespace App\Enum;

enum TaskOrder: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public function getLabel(): string
    {
        return match ($this) {
            self::ASC => 'По возрастанию',
            self::DESC => 'По убыванию',
        };
    }

    public function isAsc(): bool
    {
        return $this === self::ASC;
    }

    public function isDesc(): bool
    {
        return $this === self::DESC;
    }

    public function toggle(): self
    {
        return $this->isAsc() ? self::DESC : self::ASC;
    }
}
=== src/ValueObject/Pagination/Page.php ===
<?php

namespace App\ValueObject\Pagination;

class Page
{
    public private(set) int $number {
        set (int $number) {
            if ($number < 1) {
                throw new \InvalidArgumentException('Page must be >= 1');
            }
            $this->number = $number;
        }
    }

    public function __construct(int $number = 1)
    {
        $this->number = $number;
    }

    public function __toString(): string
    {
        return (string) $this->number;
    }
}
=== src/ValueObject/Pagination/Limit.php ===
<?php

namespace App\ValueObject\Pagination;

class Limit
{
    public private(set) int $value {
        set (int $value) {
            if ($value < 1 || $value > 100) {
                throw new \InvalidArgumentException('Limit must be between 1 and 100');
            }
            $this->value = $value;
        }
    }

    public function __construct(int $value = 10)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
=== src/Repository/TaskRepository.php ===
<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    //    /**
    //     * @return Task[] Returns an array of Task objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Task
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
=== src/Kernel.php ===
<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
