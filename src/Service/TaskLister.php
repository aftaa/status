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
