<?php

namespace App\Tests\Service;

use App\Dto\PaginatedResult;
use App\Dto\TaskListQuery;
use App\Enum\TaskFilter;
use App\Enum\TaskOrder;
use App\Enum\TaskSort;
use App\Service\CachedTaskLister;
use App\Service\TaskListerInterface;
use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class CachedTaskListerTest extends TestCase
{
    public function testGetFilteredAndSortedTasksUsesCache(): void
    {
        $delegateMock = $this->createMock(TaskListerInterface::class);
        $expectedResult = new PaginatedResult([], 1, 0, 10);

        $delegateMock->expects($this->once())
            ->method('getFilteredAndSortedTasks')
            ->willReturn($expectedResult);

        $cache = new TagAwareAdapter(new ArrayAdapter());

        // Создаём мок для логгера
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never()) // Логгер не должен вызываться в этом тесте
        ->method('error');

        $cachedLister = new CachedTaskLister(
            $delegateMock,
            $cache,
            $logger // 👈 Передаём логгер
        );

        $query = new TaskListQuery(
            sort: TaskSort::NAME,
            order: TaskOrder::ASC,
            filter: TaskFilter::ALL,
            page: new Page(1),
            limit: new Limit(10),
        );

        $result1 = $cachedLister->getFilteredAndSortedTasks($query);
        $result2 = $cachedLister->getFilteredAndSortedTasks($query);

        $this->assertSame($expectedResult, $result1);
        $this->assertEquals($expectedResult, $result2);
    }
}
