<?php

namespace App\Tests\Service;

use App\Dto\PaginatedResult;
use App\Dto\TaskListQuery;
use App\Enum\TaskFilter;
use App\Enum\TaskOrder;
use App\Enum\TaskSort;
use App\Factory\SortingStrategyFactory;
use App\Factory\SpecificationFactory;
use App\Repository\TaskRepository;
use App\Service\DoctrinePaginator;
use App\Service\TaskLister;
use App\Specification\SpecificationInterface;
use App\Strategy\SortingStrategyInterface;
use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class TaskListerTest extends TestCase
{
    public function testApplyFilterAndSorting(): void
    {
        $qb = $this->createMock(QueryBuilder::class);

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
            ->with($qb, new Page(2), new Limit(5))
            ->willReturn(new PaginatedResult([], 2, 0, 5));

        $query = new TaskListQuery(
            sort: TaskSort::NAME,
            order: TaskOrder::ASC,
            filter: TaskFilter::COMPLETED,
            page: new Page(2),
            limit: new Limit(5),
        );

        $service = new TaskLister($repository, $sortingStrategyFactory, $specificationFactory, $paginator);
        $result = $service->getFilteredAndSortedTasks($query);

        $this->assertInstanceOf(PaginatedResult::class, $result);
    }
}
