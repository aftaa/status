<?php

namespace App\Tests\Service;

use App\Dto\TaskListQuery;
use App\Factory\SortingStrategyFactory;
use App\Factory\SpecificationFactory;
use App\Service\DoctrinePaginator;
use App\Service\TaskLister;
use App\Specification\SpecificationInterface;
use App\Strategy\SortingStrategyInterface;
use App\Repository\TaskRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class TaskListerTest extends TestCase
{
    public function testApplyFilterAndSorting(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->method('getQuery')->willReturnSelf();
//        $qb->method('getResult')->willReturn([]);

        $repository = $this->createMock(TaskRepository::class);
        $repository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $specification = $this->createMock(SpecificationInterface::class);
        $specification->expects($this->once())
            ->method('apply')
            ->with($qb);

        $specificationFactory = $this->createMock(SpecificationFactory::class);
        $specificationFactory->expects($this->once())
            ->method('getSpecification')
            ->with('completed')
            ->willReturn($specification);

        $sortingStrategy = $this->createMock(SortingStrategyInterface::class);
        $sortingStrategy->expects($this->once())
            ->method('apply')
            ->with($qb, 'asc');

        $sortingStrategyFactory = $this->createMock(SortingStrategyFactory::class);
        $sortingStrategyFactory->expects($this->once())
            ->method('getStrategy')
            ->with('name')
            ->willReturn($sortingStrategy);

        $paginator = $this->createMock(DoctrinePaginator::class);
        $paginator->expects($this->once())
            ->method('paginate')
            ->with($qb, 2, 5)
            ->willReturn(new \App\Dto\PaginatedResult([], 2, 0, 5));

        $query = new TaskListQuery('name', 'asc', 'completed');

        $service = new TaskLister($repository, $sortingStrategyFactory, $specificationFactory, $paginator);
        $result = $service->getFilteredAndSortedTasks($query, 2, 5);

        $this->assertInstanceOf(\App\Dto\PaginatedResult::class, $result);
    }
}
