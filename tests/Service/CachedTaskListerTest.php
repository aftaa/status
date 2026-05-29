<?php

// tests/Service/CachedTaskListerTest.php
namespace App\Tests\Service;

use App\Dto\PaginatedResult;
use App\Dto\TaskListQuery;
use App\Service\CachedTaskLister;
use App\Service\TaskListerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class CachedTaskListerTest extends TestCase
{
    public function testGetFilteredAndSortedTasksUsesCache(): void
    {
// 1. Создаем мок оригинального сервиса TaskLister
        $delegateMock = $this->createMock(TaskListerInterface::class);
        $expectedResult = new PaginatedResult([], 1, 0, 10);

// Мы ОЖИДАЕМ, что оригинальный сервис вызовется ровно ОДИН раз
        $delegateMock->expects($this->once())
            ->method('getFilteredAndSortedTasks')
            ->willReturn($expectedResult);

// 2. Используем реальный, но «легкий» пул кэша в памяти (ArrayAdapter)
// Он идеально подходит для тестов, так как реализует TagAwareCacheInterface
        $cache = new TagAwareAdapter(new ArrayAdapter());

// 3. Создаем декоратор
        $cachedLister = new CachedTaskLister($delegateMock, $cache);

        $query = new TaskListQuery('name', 'asc', 'all');

// ПЕРВЫЙ ВЫЗОВ: Данных в кэше нет, вызовется $delegateMock
        $result1 = $cachedLister->getFilteredAndSortedTasks($query, 1, 10);

// ВТОРОЙ ВЫЗОВ: Данные берутся из ArrayAdapter, $delegateMock НЕ вызывается
        $result2 = $cachedLister->getFilteredAndSortedTasks($query, 1, 10);

// Проверяем, что оба раза вернулся правильный объект
        $this->assertSame($expectedResult, $result1);
        $this->assertEquals($expectedResult, $result2);
    }
}
